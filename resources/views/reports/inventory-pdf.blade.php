<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><style>body{font-family:DejaVu Sans,sans-serif;font-size:12px}table{width:100%;border-collapse:collapse}th,td{padding:8px;border:1px solid #ddd;text-align:left}</style></head>
<body>
    <h1>Inventory Report</h1>
    <table>
        <thead><tr><th>SKU</th><th>Product</th><th>Category</th><th>Supplier</th><th>Stock</th></tr></thead>
        <tbody>@foreach($products as $product)<tr><td>{{ $product->sku }}</td><td>{{ $product->name }}</td><td>{{ $product->category?->name }}</td><td>{{ $product->supplier?->name }}</td><td>{{ $product->stocks->sum('quantity') }}</td></tr>@endforeach</tbody>
    </table>
</body>
</html>
