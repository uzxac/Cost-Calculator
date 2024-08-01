<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calculation;

class CalculationController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15); // Sahifadagi elementlar soni (default: 15)
        $calculations = Calculation::paginate($perPage);
        return response()->json($calculations);
    }
    public function calculate(Request $request)
    {
        $data = $request->validate([
            'purchase_price' => 'required|numeric', // Tovar sotib olingan narxi
            'logistics_cost' => 'required|numeric', // Tovar olib kelish narxi
            'quantity' => 'required|integer', // partiyadagi tovarlar soni
            'tax_rate' => 'nullable|numeric', // soliq stavkasi, foizda
            'selling_price' => 'nullable|numeric', // Tovar sotish narxi
            'margin_percentage' => 'nullable|numeric', // Marjinallik foizi, foizda
            'category_commission_fbs' => 'required|numeric', // FBS komissiyasi, foizda
            'category_commission_fbo' => 'required|numeric', // FBO komissiyasi, foizda
            'height' => 'required|numeric', // Tovar balandligi, sm
            'length' => 'required|numeric', // Tovar uzunligi, sm
            'depth' => 'required|numeric', // Tovar eni, sm
        ]);

// Logistika narxi 1 dona uchun
        $data['logistics_cost_per_unit'] = $data['logistics_cost'] / $data['quantity'];

// Agar sotish narxi berilmasa, marjinallik foizi asosida hisoblash
        if (is_null($data['selling_price']) && !is_null($data['margin_percentage'])) {
            $data['selling_price'] = $data['purchase_price'] * (1 + $data['margin_percentage'] / 100);
        }

// Tovar hajmi litrda
        $data['volume_liters'] = ($data['height'] * $data['length'] * $data['depth']) / 1000;

// Mijozga logistika narxi
        $data['customer_logistics_cost'] = $data['volume_liters'] * 50;

// Marketplace komissiyalari
        $data['fbs_commission'] = $data['selling_price'] * ($data['category_commission_fbs'] / 100);
        $data['fbo_commission'] = $data['selling_price'] * ($data['category_commission_fbo'] / 100);

// 1 dona tovar uchun umumiy narx
        $total_cost_per_unit = $data['purchase_price'] + $data['logistics_cost_per_unit'];
        if (!is_null($data['tax_rate'])) {
            $total_cost_per_unit += $total_cost_per_unit * ($data['tax_rate'] / 100);
        }

// Foyda hisoblash
        $data['profit_per_unit_fbs'] = $data['selling_price'] - ($total_cost_per_unit + $data['fbs_commission'] + $data['customer_logistics_cost']);
        $data['profit_per_unit_fbo'] = $data['selling_price'] - ($total_cost_per_unit + $data['fbo_commission'] + $data['customer_logistics_cost']);

// Partiya uchun umumiy foyda
        $data['total_profit_fbs'] = $data['profit_per_unit_fbs'] * $data['quantity'];
        $data['total_profit_fbo'] = $data['profit_per_unit_fbo'] * $data['quantity'];

// Hisoblangan ma'lumotlarni saqlash
        if($request->has('save') && $request->input('save')) {
            $calculation = Calculation::create($data);
            $data['id'] = $calculation->id;
        }

        return response()->json($data, 201);
    }
    public function recalculation($id)
    {
        $calculation = Calculation::findOrFail($id);

        $data = [
            'purchase_price' => $calculation->purchase_price,
            'logistics_cost' => $calculation->logistics_cost,
            'quantity' => $calculation->quantity,
            'tax_rate' => $calculation->tax_rate,
            'selling_price' => $calculation->selling_price,
            'margin_percentage' => $calculation->margin_percentage,
            'category_commission_fbs' => $calculation->category_commission_fbs,
            'category_commission_fbo' => $calculation->category_commission_fbo,
            'height' => $calculation->height,
            'length' => $calculation->length,
            'depth' => $calculation->depth,
            'save' => false,
        ];
        $data = $this->calculate(new Request($data));
        return response()->json($data, 201);
    }
}
