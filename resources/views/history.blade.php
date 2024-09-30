<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>History</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    @vite('resources/css/app.css')
</head>
<body class="font-sans antialiased dark:bg-gray-500">

<div class="container mx-auto p-4">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $game['name'] }} - История по регионам</h1>

        <a href="{{ route('index') }}" class="inline-block bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Назад
        </a>
    </div>

    @foreach ($history as $region => $key)
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-2 text-gray-700 dark:text-gray-300">Регион {{ ucfirst($region) }}</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300 dark:border-gray-600">
                    <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700">
                        <th class="py-2 px-4 text-center text-gray-700 dark:text-gray-300">Дата</th>
                        <th class="py-2 px-4 text-center text-gray-700 dark:text-gray-300">Позиция</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($key as $item)
                        <tr class="bg-white dark:bg-gray-800">
                            <td class="border-t border-gray-300 dark:border-gray-600 px-4 py-2 text-center text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::make($item['date'])->format('d.m.Y') }}
                            </td>
                            <td class="border-t border-gray-300 dark:border-gray-600 px-4 py-2 text-center text-gray-900 dark:text-white">
                                {{ $item['position'] }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
</div>

</body>
</html>
