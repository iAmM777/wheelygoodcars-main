<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 24px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            font-size: 12px;
            line-height: 1.45;
        }

        .sheet {
            border: 1px solid #d1d5db;
            border-radius: 16px;
            padding: 24px;
        }

        .brandline {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 18px;
        }

        .eyebrow {
            text-transform: uppercase;
            letter-spacing: 0.16em;
            color: #6b7280;
            font-size: 10px;
            margin-bottom: 6px;
        }

        h1 {
            margin: 0;
            font-size: 24px;
        }

        .badge {
            display: inline-block;
            background: #111827;
            color: #fff;
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 12px;
            white-space: nowrap;
        }

        .summary {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 18px;
        }

        .grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        .grid td {
            width: 50%;
            vertical-align: top;
            padding: 0 8px 10px 0;
        }

        .panel {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 12px 14px;
            background: #fff;
        }

        .label {
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-size: 9px;
            margin-bottom: 4px;
        }

        .value {
            font-size: 14px;
            font-weight: 600;
        }

        .price {
            font-size: 22px;
            font-weight: 700;
            color: #047857;
        }

        .specs {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        .specs td {
            width: 25%;
            padding: 0 8px 8px 0;
        }

        .note {
            margin-top: 18px;
            padding-top: 12px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 10px;
        }

        .tag {
            display: inline-block;
            border: 1px solid #d1d5db;
            border-radius: 999px;
            padding: 4px 9px;
            margin: 0 6px 6px 0;
            background: #fff;
        }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="brandline">
            <div>
                <div class="eyebrow">WheelyGoodCars</div>
                <h1>{{ $car->brand }} {{ $car->model }}</h1>
                <div style="margin-top: 8px; color: #6b7280;">Bouwjaar {{ $car->production_year ?? 'onbekend' }} · Kenteken {{ $car->license_plate }}</div>
            </div>
            <div class="badge">{{ $car->license_plate }}</div>
        </div>

        <div class="summary">
            <div class="label">Vraagprijs</div>
            <div class="price">€{{ number_format((float) $car->price, 2, ',', '.') }}</div>
        </div>

        <table class="grid">
            <tr>
                <td>
                    <div class="panel">
                        <div class="label">Kilometerstand</div>
                        <div class="value">{{ number_format((int) $car->mileage, 0, ',', '.') }} km</div>
                    </div>
                </td>
                <td>
                    <div class="panel">
                        <div class="label">Kleur</div>
                        <div class="value">{{ $car->color ?? 'Niet gespecificeerd' }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="panel">
                        <div class="label">Deuren</div>
                        <div class="value">{{ $car->doors ?? '-' }}</div>
                    </div>
                </td>
                <td>
                    <div class="panel">
                        <div class="label">Zitplaatsen</div>
                        <div class="value">{{ $car->seats ?? '-' }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="panel">
                        <div class="label">Gewicht</div>
                        <div class="value">{{ $car->weight ?? '-' }} kg</div>
                    </div>
                </td>
                <td>
                    <div class="panel">
                        <div class="label">Aanbieder</div>
                        <div class="value">{{ $car->user->name }}</div>
                    </div>
                </td>
            </tr>
        </table>

        @if ($car->tags->isNotEmpty())
            <div class="panel" style="margin-bottom: 18px;">
                <div class="label">Tags</div>
                <div>
                    @foreach ($car->tags as $tag)
                        <span class="tag">{{ $tag->name }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        <table class="specs">
            <tr>
                <td><div class="panel"><div class="label">Status</div><div class="value">Te koop</div></div></td>
                <td><div class="panel"><div class="label">Aangeboden</div><div class="value">{{ $car->created_at->format('d-m-Y') }}</div></div></td>
                <td><div class="panel"><div class="label">Kenteken</div><div class="value">{{ $car->license_plate }}</div></div></td>
                <td><div class="panel"><div class="label">Voorraad</div><div class="value">Klantvriendelijk</div></div></td>
            </tr>
        </table>

        <div class="note">
            Deze PDF is automatisch gegenereerd uit de actuele autogegevens en is bedoeld om af te drukken en in de auto te plaatsen.
        </div>
    </div>
</body>
</html>