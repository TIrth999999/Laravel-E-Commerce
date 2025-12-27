<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index()
    {
        $taxes = Tax::latest()->get();
        return view('admin.taxes.index', compact('taxes'));
    }

    public function create()
    {
        return view('admin.taxes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required|in:flat,percentage',
            'value' => 'required|numeric|min:0',
            'apply_on' => 'required|in:after_discount,before_discount',
        ]);

        Tax::create($request->all());
        return redirect()->route('admin.taxes.index')->with('success', 'Tax created');
    }

    public function show($id)
    {
        $tax = Tax::findOrFail($id);
        return view('admin.taxes.show', compact('tax'));
    }

    public function edit($id)
    {
        $tax = Tax::findOrFail($id);
        return view('admin.taxes.edit', compact('tax'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required|in:flat,percentage',
            'value' => 'required|numeric|min:0',
            'apply_on' => 'required|in:after_discount,before_discount',
        ]);

        $tax = Tax::findOrFail($id);
        $tax->update($request->all());
        return redirect()->route('admin.taxes.index')->with('success', 'Tax updated');
    }

    public function destroy($id)
    {
        $tax = Tax::findOrFail($id);
        $tax->delete();
        return redirect()->route('admin.taxes.index')->with('success', 'Tax deleted');
    }
}
