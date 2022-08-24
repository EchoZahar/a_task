<!DOCTYPE html>
<html>
<head>
    <title>New customer request</title>
</head>
<body>
<h1>Customer name: {{ $customerRequest->name }}</h1>
<p>New request #{{ $customerRequest->id }}</p>
<p>{{ $customerRequest->email }}</p>

<p>{{ $customerRequest->message }}</p>

<p>Thank you</p>
</body>
</html>
