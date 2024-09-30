<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        @vite('resources/css/app.css')
    </head>
    <body class="font-sans antialiased dark:bg-gray-600 uppercase">
        <div class="container mx-auto p-4">
            <h1 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white">Список Игр</h1>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="text-lg">
                        <tr class="bg-gray-100 dark:bg-gray-700">
                            <th class="py-2 text-center text-gray-700 dark:text-gray-300">Название</th>
                            <th class="py-2 text-center w-auto text-gray-700 dark:text-gray-300">US</th>
                            <th class="py-2 text-center w-auto text-gray-700 dark:text-gray-300">GB</th>
                            <th class="py-2 text-center w-auto text-gray-700 dark:text-gray-300">DE</th>
                            <th class="py-2 text-center w-auto text-gray-700 dark:text-gray-300">JA</th>
                            <th class="py-2 text-center text-gray-700 dark:text-gray-300">Дата релиза</th>
                            <th class="py-2 text-center text-gray-700 dark:text-gray-300">Издатель</th>
                            <th class="py-2 text-center text-gray-700 dark:text-gray-300">Жанр</th>
                            <th class="py-2 text-center text-gray-700 dark:text-gray-300">Рейтинг</th>
                            <th class="py-2 text-center text-gray-700 dark:text-gray-300">Цена</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach ($games as $game)
                            <tr class="hover:bg-gray-200 dark:hover:bg-gray-700 cursor-pointer"
                                onclick="window.location='{{ route('game.history', ['id' => $game['id']]) }}'">
                                <td class="border-t-2 border-gray-300 dark:border-gray-600 px-4 py-2 text-center text-gray-900 dark:text-white">
                                    {{ $game['name'] }}
                                </td>
                                @foreach ($game['positions'] as $region => $position)
                                    <td class="border-t-2 border-gray-300 dark:border-gray-600 min-w-[50px] w-auto py-2 text-center text-gray-900 dark:text-white">
                                        {{ $position }}
                                        @php
                                            $history = \App\Models\History::where('game_id', $game['id'])
                                                ->where('region', $region)
                                                ->orderBy('date', 'desc')
                                                ->get();

                                            $isChange = false;
                                            $change = 0;

                                            if ($history->count() > 1 && $position != '-') {
                                                $lastHistory = $history->get(0);
                                                $change = $position - $lastHistory->position;
                                            }

                                            if ($change != 0) {
                                                $isChange = true;
                                            }
                                        @endphp
                                        @if ($isChange)
                                            <span class="text-sm
                                                {{ $change < 0 ? 'text-green-600' : ($change > 0 ? 'text-red-600' : 'text-gray-500') }}
                                                ">
                                                ({{ $change < 0 ? '+' : '-' }}{{  abs($change) }})
                                            </span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="border-t-2 border-gray-300 dark:border-gray-600 px-4 py-2 text-center text-gray-900 dark:text-white">{{ \Carbon\Carbon::make($game['release_date'])->format('d.m.Y') }}</td>
                                <td class="border-t-2 border-gray-300 dark:border-gray-600 px-4 py-2 text-center text-gray-900 dark:text-white">{{ $game['publisher'] }}</td>
                                <td class="border-t-2 border-gray-300 dark:border-gray-600 px-4 py-2 text-center text-gray-900 dark:text-white">{{ $game['genres'] }}</td>
                                <td class="border-t-2 border-gray-300 dark:border-gray-600 px-4 py-2 text-center text-gray-900 dark:text-white">{{ $game['rating'] }}</td>
                                <td class="border-t-2 border-gray-300 dark:border-gray-600 px-4 py-2 text-center text-gray-900 dark:text-white">{{ $game['price'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </body>
</html>
