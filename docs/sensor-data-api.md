# API Dokumentasi: Sensor Data Endpoint

**Endpoint:** `POST /api/sensor-data`
**Versi:** 1.1.0
**Tanggal Update:** 13 Mei 2026
**Author:** STAS-RG Team

---

## Overview

Endpoint ini menerima data dari hardware sensor (ESP32/Heltec V3) dan menyimpannya ke database. Data disimpan ke dua tabel secara bersamaan:

| Tabel | Isi |
|---|---|
| `sensor_readings` | **Semua parameter** — digunakan untuk dashboard |
| `water_quality_readings` | Parameter kualitas air (turbidity, EC, TDS, ORP) — untuk analytics lanjutan |

---

## Request

**Method:** `POST`
**URL:** `https://dev-aquaculture.stas-rg.com/api/sensor-data`
**Auth:** Tidak diperlukan (public endpoint)

### Headers

```
Content-Type: application/json
User-Agent: ESP32-STAS-RG
```

### Body (JSON)

```json
{
  "device_code":        "DEV-9TQZXJVL",
  "env_temperature":    0.00,
  "water_temperature":  24.85,
  "ph":                 6.43,
  "dissolved_oxygen":   8.20,
  "turbidity_ntu":      31.40,
  "ec_s_m":             640,
  "tds_ppm":            320,
  "tds_ec_mod":         315,
  "orp_mv":             210.00,
  "risk_level":         0.00
}
```

### Deskripsi Field

| Field | Tipe | Wajib | Satuan | Deskripsi |
|---|---|---|---|---|
| `device_code` | string | ✅ Ya | — | Kode unik device, harus terdaftar di tabel `devices` |
| `env_temperature` | float | Tidak | °C | Suhu lingkungan/udara (sensor DHT). Kirim `0` jika tidak ada sensor |
| `water_temperature` | float | Tidak | °C | Suhu air dari sensor DS18B20 |
| `ph` | float | Tidak | 0–14 | Nilai pH air |
| `dissolved_oxygen` | float | Tidak | mg/L | Dissolved Oxygen (DO) |
| `turbidity_ntu` | float | Tidak | NTU | Kekeruhan air |
| `ec_s_m` | float | Tidak | µS/cm | Electrical Conductivity |
| `tds_ppm` | float | Tidak | ppm | TDS dari modul sensor standalone |
| `tds_ec_mod` | float | Tidak | mg/L | TDS hasil perhitungan dari modul EC |
| `orp_mv` | float | Tidak | mV | Oxidation-Reduction Potential |
| `risk_level` | float | Tidak | 0–1 | Tingkat risiko toksisitas. Kirim `0` (dihitung server ke depannya) |

> **Catatan:** Hanya `device_code` yang wajib. Semua field lain nullable — kirimkan 0 jika sensor tidak tersedia, bukan null/kosong.

---

## Response

### Sukses — HTTP 201

```json
{
  "status": "success",
  "basic_id": 253,
  "quality_id": 53
}
```

| Field | Deskripsi |
|---|---|
| `basic_id` | ID record yang tersimpan di tabel `sensor_readings` |
| `quality_id` | ID record yang tersimpan di tabel `water_quality_readings` |

### Error — HTTP 400

```json
{
  "error": "Invalid device_code"
}
```

Terjadi jika `device_code` tidak terdaftar di database atau tidak dikirimkan.

---

## Implementasi Hardware (Arduino / ESP32)

### Payload Builder

```cpp
String payload = "{";
payload += "\"device_code\":\"" + String(device_code) + "\",";
payload += "\"env_temperature\":0.00,";
payload += "\"water_temperature\":" + String(suhuC, 2) + ",";
payload += "\"ph\":" + String(phVal, 2) + ",";
payload += "\"dissolved_oxygen\":" + String(doVal, 2) + ",";
payload += "\"turbidity_ntu\":" + String(ntuVal, 2) + ",";
payload += "\"ec_s_m\":" + String(ecVal, 0) + ",";
payload += "\"tds_ppm\":" + String(tdsStdVal, 0) + ",";
payload += "\"tds_ec_mod\":" + String(tdsEC, 0) + ",";
payload += "\"orp_mv\":" + String(orpVal, 2) + ",";
payload += "\"risk_level\":0.00";
payload += "}";
```

### Mapping Variabel Hardware ke Field JSON

| Field JSON | Variabel C++ | Sumber Sensor |
|---|---|---|
| `env_temperature` | — | Hardcode `0.00` (tidak ada sensor DHT) |
| `water_temperature` | `suhuC` | DS18B20 (pin 4) |
| `ph` | `phVal` | ADS1115_A (pin A2), gain 1 |
| `dissolved_oxygen` | `doVal` | ADS1115_B (pin A0), gain 1 |
| `turbidity_ntu` | `ntuVal` | ADS1115_A (pin A0), gain 0 |
| `ec_s_m` | `ecVal` | ADS1115_B (pin A1), dihitung dari `tdsEC * 2.0` |
| `tds_ppm` | `tdsStdVal` | ADS1115_A (pin A1), formula kuadrat |
| `tds_ec_mod` | `tdsEC` | ADS1115_B (pin A1), formula kuadrat dari modul EC |
| `orp_mv` | `orpVal` | ADS1115_B (pin A2), menggunakan modul pH |
| `risk_level` | — | Hardcode `0.00` |

### Konfigurasi HTTP

```cpp
WiFiClientSecure client;
client.setInsecure();  // Skip SSL verification (development)

HTTPClient http;
http.begin(client, serverName);
http.addHeader("Content-Type", "application/json");
http.addHeader("User-Agent", "ESP32-STAS-RG");

int httpResponseCode = http.POST(payload);
```

---

## Arsitektur Penyimpanan Data

```
POST /api/sensor-data
        |
        v
SensorDataController::store()
        |
        |---> SensorReading::create()       --> tabel: sensor_readings
        |     Fields: env_temperature, water_temperature, ph,
        |             dissolved_oxygen, risk_level, turbidity_ntu,
        |             ec_s_m, tds_ppm, tds_ec_mod, orp_mv
        |
        |---> WaterQualityReading::create() --> tabel: water_quality_readings
              Fields: turbidity_ntu, ec_s_m, tds_ppm, tds_ec_mod, orp_mv
```

**Dashboard** membaca dari `sensor_readings` via relasi `Device::readings()`.

---

## Cara Test Lokal

```bash
curl -X POST http://localhost:8000/api/sensor-data \
  -H "Content-Type: application/json" \
  -d '{
    "device_code": "DEV-47GLFAZR",
    "env_temperature": 0,
    "water_temperature": 25.5,
    "ph": 7.0,
    "dissolved_oxygen": 6.8,
    "turbidity_ntu": 3.5,
    "ec_s_m": 640,
    "tds_ppm": 320,
    "tds_ec_mod": 315,
    "orp_mv": 210,
    "risk_level": 0
  }'
```

Expected response:
```json
{"status":"success","basic_id":253,"quality_id":53}
```

---

## Checklist Deploy ke Production

Setelah ada perubahan schema atau controller:

```bash
git pull
php artisan migrate --force
php artisan view:clear
php artisan optimize:clear
```

Verifikasi migration:
```bash
php artisan migrate:status
# Pastikan semua migration status = Ran
```

---

## Changelog

| Versi | Tanggal | Perubahan |
|---|---|---|
| 1.1.0 | 13 Mei 2026 | Fix: `turbidity_ntu`, `ec_s_m`, `tds_ppm`, `orp_mv` kini tersimpan ke `sensor_readings` sehingga tampil di dashboard. Tambah `tds_ec_mod` ke kedua tabel. |
| 1.0.1 | 5 Mei 2026 | Fix: Tambah `use App\Models\WaterQualityReading` yang hilang di controller. |
| 1.0.0 | 20 Jan 2026 | Initial: Endpoint `/api/sensor-data` dengan dual-table storage. |
