<?php

namespace App\Http\Controllers;

use App\Models\Isle;
use App\Models\Loan;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $isMaster = $user->role->nombre === 'master';
        $isAdmin = $user->role->nombre === 'admin';

        $query = Loan::with(['user', 'location', 'isle'])
            ->where('deleted', false)
            ->orderBy('loan_date', 'desc')
            ->orderBy('id', 'desc');

        if (! $isMaster) {
            $query->where('location_id', $user->location_id);
        }

        if ($user->role->nombre === 'worker' && $user->isle_id) {
            $query->where('isle_id', $user->isle_id);
        }

        if ($request->filled('location_id') && $isMaster) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('loan_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('loan_date', '<=', $request->end_date);
        }

        $loans = $query->paginate(15)->appends($request->query());

        $totalsQuery = clone $query;
        $allForTotals = $totalsQuery->get();
        $totalLoaned = $allForTotals->sum('loan_amount');
        $totalRecovered = $allForTotals->sum('recovered_amount');
        $totalPending = $allForTotals->sum(function ($loan) {
            return $loan->balance;
        });

        $locations = $isMaster
            ? Location::where('deleted', 0)->orderBy('name')->get()
            : Location::where('id', $user->location_id)->get();

        $isles = Isle::where('deleted', 0)
            ->when(! $isMaster && ! $isAdmin, function ($q) use ($user) {
                if ($user->isle_id) {
                    $q->where('id', $user->isle_id);
                } else {
                    $q->where('location_id', $user->location_id);
                }
            })
            ->when(! $isMaster && $isAdmin, function ($q) use ($user) {
                $q->where('location_id', $user->location_id);
            })
            ->orderBy('name')
            ->get();

        return view('loans.index', compact('loans', 'locations', 'isles', 'totalLoaned', 'totalRecovered', 'totalPending', 'isMaster'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'loan_date' => 'required|date',
            'loan_amount' => 'required|numeric|min:0.01',
            'send_method' => 'nullable|string|max:50',
            'due_date' => 'nullable|date',
            'recovered_amount' => 'nullable|numeric|min:0',
            'collection_method' => 'nullable|string|max:50',
            'collection_date' => 'nullable|date',
            'isle_id' => 'required|exists:isles,id',
        ]);

        return $this->saveLoan(new Loan(), $data);
    }

    public function update(Request $request, $id)
    {
        $loan = Loan::where('deleted', false)->findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'loan_date' => 'required|date',
            'loan_amount' => 'required|numeric|min:0.01',
            'send_method' => 'nullable|string|max:50',
            'due_date' => 'nullable|date',
            'recovered_amount' => 'nullable|numeric|min:0',
            'collection_method' => 'nullable|string|max:50',
            'collection_date' => 'nullable|date',
            'isle_id' => 'required|exists:isles,id',
        ]);

        return $this->saveLoan($loan, $data);
    }

    public function destroy($id)
    {
        $loan = Loan::where('deleted', false)->findOrFail($id);

        DB::beginTransaction();
        try {
            $this->reverseCashImpact($loan);
            $loan->update(['deleted' => true]);
            DB::commit();

            return response()->json(['success' => true, 'message' => 'Prestamo eliminado correctamente.']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    protected function saveLoan(Loan $loan, array $data)
    {
        if ((float) ($data['recovered_amount'] ?? 0) > 0 && empty($data['collection_method'])) {
            return response()->json([
                'success' => false,
                'message' => 'Seleccione el medio de cobro para registrar una recuperacion.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $isNew = ! $loan->exists;
            if (! $isNew) {
                $this->reverseCashImpact($loan);
            }

            $isle = Isle::lockForUpdate()->findOrFail($data['isle_id']);
            if (! $this->canAccessIsle($isle)) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'No tiene acceso a esta isla.'], 403);
            }

            $loanAmount = (float) $data['loan_amount'];
            $recoveredAmount = min((float) ($data['recovered_amount'] ?? 0), $loanAmount);

            $loan->fill([
                'user_id' => $isNew ? Auth::id() : $loan->user_id,
                'location_id' => $isle->location_id,
                'isle_id' => $isle->id,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'loan_date' => $data['loan_date'],
                'loan_amount' => $loanAmount,
                'send_method' => $data['send_method'] ?? null,
                'due_date' => $data['due_date'] ?? null,
                'recovered_amount' => $recoveredAmount,
                'collection_method' => $data['collection_method'] ?? null,
                'collection_date' => $data['collection_date'] ?? null,
                'status' => $this->resolveStatus($loanAmount, $recoveredAmount, $data['due_date'] ?? null),
                'deleted' => false,
            ]);
            $loan->save();

            $this->applyCashImpact($loan);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $isNew ? 'Prestamo registrado correctamente.' : 'Prestamo actualizado correctamente.',
                'loan' => $loan,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    protected function resolveStatus(float $loanAmount, float $recoveredAmount, $dueDate): string
    {
        if ($recoveredAmount >= $loanAmount) {
            return 'paid';
        }

        if ($dueDate && now()->startOfDay()->gt(\Carbon\Carbon::parse($dueDate)->startOfDay())) {
            return 'overdue';
        }

        return $recoveredAmount > 0 ? 'partial' : 'pending';
    }

    protected function applyCashImpact(Loan $loan): void
    {
        $isle = Isle::lockForUpdate()->find($loan->isle_id);
        if (! $isle) {
            return;
        }

        if ($this->isCashMethod($loan->send_method)) {
            $isle->decrement('cash_amount', $loan->loan_amount);
        }

        if ($loan->collection_method && $this->isCashMethod($loan->collection_method) && (float) $loan->recovered_amount > 0) {
            $isle->increment('cash_amount', $loan->recovered_amount);
        }
    }

    protected function reverseCashImpact(Loan $loan): void
    {
        $isle = Isle::lockForUpdate()->find($loan->isle_id);
        if (! $isle) {
            return;
        }

        if ($this->isCashMethod($loan->send_method)) {
            $isle->increment('cash_amount', $loan->loan_amount);
        }

        if ($loan->collection_method && $this->isCashMethod($loan->collection_method) && (float) $loan->recovered_amount > 0) {
            $isle->decrement('cash_amount', $loan->recovered_amount);
        }
    }

    protected function isCashMethod($method): bool
    {
        $method = mb_strtolower(trim((string) $method));
        return $method === '' || mb_strpos($method, 'efectivo') !== false;
    }

    protected function canAccessIsle(Isle $isle): bool
    {
        $user = Auth::user();

        if ($user->role->nombre === 'master') {
            return true;
        }

        if ((int) $isle->location_id !== (int) $user->location_id) {
            return false;
        }

        return $user->role->nombre !== 'worker'
            || empty($user->isle_id)
            || (int) $user->isle_id === (int) $isle->id;
    }
}
