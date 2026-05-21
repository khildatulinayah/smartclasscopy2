# API Dokumentasi: Holiday Management (Hari Libur)

## Overview
API untuk mengelola hari libur nasional/tanggal merah dalam sistem absensi. API ini memungkinkan untuk membuat, membaca, mengubah, dan menghapus hari libur. Terintegrasi dengan sistem absensi untuk mengecualikan hari libur dari penghitungan kehadiran.

## Base URL
```
http://localhost/sekretaris/api
```

## Authentication
Semua endpoint memerlukan login sebagai **Sekretaris** (role: sekretaris)

## Response Format
Semua response dalam format JSON dengan struktur:

### Success Response (2xx)
```json
{
    "success": true,
    "data": { ... },
    "message": "Success message"
}
```

### Error Response (4xx, 5xx)
```json
{
    "success": false,
    "message": "Error message",
    "errors": { ... }  // Hanya untuk validation errors
}
```

---

## API Endpoints

### 1. GET /holidays
**Daftar semua hari libur dengan filter dan paginasi**

**Method:** `GET`

**URL:** `/sekretaris/api/holidays`

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| month | integer | No | Filter bulan (1-12) |
| year | integer | No | Filter tahun (2020-2030) |
| start_date | date | No | Filter dari tanggal (Y-m-d) |
| end_date | date | No | Filter sampai tanggal (Y-m-d) |
| search | string | No | Cari berdasarkan keterangan |
| sort_by | string | No | Sorting: date, note (default: date) |
| sort_order | string | No | Urutan: asc, desc (default: asc) |
| per_page | integer | No | Jumlah item per halaman (default: 50) |

**Example Request:**
```bash
curl -X GET "http://localhost/sekretaris/api/holidays?month=5&year=2026&per_page=10" \
  -H "Accept: application/json"
```

**Success Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "date": "2026-05-01",
            "note": "Hari Libur Nasional - Hari Buruh",
            "created_by": 2,
            "creator_name": "Admin",
            "created_at": "2026-05-01 08:00:00"
        },
        {
            "id": 2,
            "date": "2026-06-01",
            "note": "Hari Libur Nasional - Hari Pancasila",
            "created_by": 2,
            "creator_name": "Admin",
            "created_at": "2026-06-01 08:00:00"
        }
    ],
    "pagination": {
        "total": 12,
        "per_page": 10,
        "current_page": 1,
        "last_page": 2,
        "from": 1,
        "to": 10
    },
    "message": "Data hari libur berhasil diambil"
}
```

---

### 2. GET /holidays/month/{month}/year/{year}
**Daftar hari libur untuk bulan dan tahun tertentu**

**Method:** `GET`

**URL:** `/sekretaris/api/holidays/month/{month}/year/{year}`

**URL Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| month | integer | Yes | Bulan (1-12) |
| year | integer | Yes | Tahun (2020-2030) |

**Example Request:**
```bash
curl -X GET "http://localhost/sekretaris/api/holidays/month/5/year/2026" \
  -H "Accept: application/json"
```

**Success Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "date": "2026-05-01",
            "day_name": "Jumat",
            "note": "Hari Libur Nasional - Hari Buruh",
            "created_by": 2,
            "creator_name": "Admin",
            "created_at": "2026-05-01 08:00:00"
        }
    ],
    "count": 1,
    "month": 5,
    "year": 2026,
    "message": "Data hari libur berhasil diambil"
}
```

**Error Response (400):**
```json
{
    "success": false,
    "message": "Bulan atau tahun tidak valid"
}
```

---

### 3. POST /holidays
**Tambah hari libur baru**

**Method:** `POST`

**URL:** `/sekretaris/api/holidays`

**Request Body:**
```json
{
    "date": "2026-05-21",
    "note": "Hari Libur Nasional - Kenaikan Isa Almasih"
}
```

**Required Fields:**
| Field | Type | Validation | Description |
|-------|------|-----------|-------------|
| date | date | Y-m-d format, unique | Tanggal hari libur |
| note | string | min:3, max:255 | Keterangan hari libur |

**Example Request:**
```bash
curl -X POST "http://localhost/sekretaris/api/holidays" \
  -H "Content-Type: application/json" \
  -d '{
    "date": "2026-05-21",
    "note": "Hari Libur Nasional - Kenaikan Isa Almasih"
  }'
```

**Success Response (201):**
```json
{
    "success": true,
    "data": {
        "id": 5,
        "date": "2026-05-21",
        "day_name": "Kamis",
        "note": "Hari Libur Nasional - Kenaikan Isa Almasih",
        "created_by": 2,
        "creator_name": "Admin",
        "created_at": "2026-05-21 10:30:00"
    },
    "message": "Hari libur berhasil ditambahkan"
}
```

**Validation Error Response (422):**
```json
{
    "success": false,
    "message": "Validasi gagal",
    "errors": {
        "date": ["Format tanggal harus Y-m-d"],
        "note": ["Keterangan minimal 3 karakter"]
    }
}
```

**Duplicate Error Response (400):**
```json
{
    "success": false,
    "message": "Tanggal 2026-05-01 sudah terdaftar sebagai hari libur: Hari Buruh"
}
```

---

### 4. GET /holidays/{id}
**Ambil detail hari libur tertentu**

**Method:** `GET`

**URL:** `/sekretaris/api/holidays/{id}`

**URL Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| id | integer | Yes | Holiday ID |

**Example Request:**
```bash
curl -X GET "http://localhost/sekretaris/api/holidays/5" \
  -H "Accept: application/json"
```

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 5,
        "date": "2026-05-21",
        "day_name": "Kamis",
        "note": "Hari Libur Nasional - Kenaikan Isa Almasih",
        "created_by": 2,
        "creator_name": "Admin",
        "created_at": "2026-05-21 10:30:00"
    },
    "message": "Data hari libur berhasil diambil"
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Hari libur tidak ditemukan"
}
```

---

### 5. PUT /holidays/{id}
**Update hari libur**

**Method:** `PUT`

**URL:** `/sekretaris/api/holidays/{id}`

**Request Body:**
```json
{
    "date": "2026-05-22",
    "note": "Hari Libur Dipindahkan - Kenaikan Isa Almasih"
}
```

**Example Request:**
```bash
curl -X PUT "http://localhost/sekretaris/api/holidays/5" \
  -H "Content-Type: application/json" \
  -d '{
    "date": "2026-05-22",
    "note": "Hari Libur Dipindahkan - Kenaikan Isa Almasih"
  }'
```

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 5,
        "date": "2026-05-22",
        "day_name": "Jumat",
        "note": "Hari Libur Dipindahkan - Kenaikan Isa Almasih",
        "created_by": 2,
        "updated_at": "2026-05-22 11:00:00"
    },
    "message": "Hari libur berhasil diperbarui"
}
```

**Validation Error Response (422):**
```json
{
    "success": false,
    "message": "Validasi gagal",
    "errors": {
        "date": ["Format tanggal harus Y-m-d"]
    }
}
```

---

### 6. DELETE /holidays/{id}
**Hapus hari libur**

**Method:** `DELETE`

**URL:** `/sekretaris/api/holidays/{id}`

**Example Request:**
```bash
curl -X DELETE "http://localhost/sekretaris/api/holidays/5" \
  -H "Accept: application/json"
```

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "id": 5,
        "date": "2026-05-22",
        "note": "Hari Libur Dipindahkan - Kenaikan Isa Almasih"
    },
    "message": "Hari libur berhasil dihapus"
}
```

**Error Response (404):**
```json
{
    "success": false,
    "message": "Hari libur tidak ditemukan"
}
```

---

### 7. POST /holidays/bulk-add
**Tambah banyak hari libur sekaligus**

**Method:** `POST`

**URL:** `/sekretaris/api/holidays/bulk-add`

**Request Body:**
```json
{
    "holidays": [
        {
            "date": "2026-08-17",
            "note": "Hari Libur Nasional - HUT RI"
        },
        {
            "date": "2026-12-25",
            "note": "Hari Libur Nasional - Natal"
        },
        {
            "date": "2026-12-26",
            "note": "Hari Libur Bersama - Cuti Bersama"
        }
    ]
}
```

**Example Request:**
```bash
curl -X POST "http://localhost/sekretaris/api/holidays/bulk-add" \
  -H "Content-Type: application/json" \
  -d '{
    "holidays": [
        {"date": "2026-08-17", "note": "Hari Libur Nasional - HUT RI"},
        {"date": "2026-12-25", "note": "Hari Libur Nasional - Natal"}
    ]
  }'
```

**Success Response (201):**
```json
{
    "success": true,
    "data": [
        {
            "id": 6,
            "date": "2026-08-17",
            "day_name": "Senin",
            "note": "Hari Libur Nasional - HUT RI",
            "created_at": "2026-08-17 08:00:00"
        },
        {
            "id": 7,
            "date": "2026-12-25",
            "day_name": "Jumat",
            "note": "Hari Libur Nasional - Natal",
            "created_at": "2026-12-25 08:00:00"
        }
    ],
    "errors": [],
    "message": "2 hari libur berhasil ditambahkan",
    "created_count": 2,
    "failed_count": 0
}
```

**Partial Success Response (207):**
```json
{
    "success": false,
    "data": [
        {
            "id": 6,
            "date": "2026-08-17",
            "day_name": "Senin",
            "note": "Hari Libur Nasional - HUT RI",
            "created_at": "2026-08-17 08:00:00"
        }
    ],
    "errors": [
        {
            "index": 1,
            "date": "2026-05-01",
            "message": "Tanggal sudah terdaftar: Hari Buruh"
        }
    ],
    "message": "1 hari libur berhasil ditambahkan, 1 hari libur gagal ditambahkan",
    "created_count": 1,
    "failed_count": 1
}
```

---

### 8. GET /holidays-summary
**Ringkasan hari libur dalam bulan**

**Method:** `GET`

**URL:** `/sekretaris/api/holidays-summary`

**Query Parameters:**
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| month | integer | No | Filter bulan (default: bulan saat ini) |
| year | integer | No | Filter tahun (default: tahun saat ini) |

**Example Request:**
```bash
curl -X GET "http://localhost/sekretaris/api/holidays-summary?month=5&year=2026" \
  -H "Accept: application/json"
```

**Success Response (200):**
```json
{
    "success": true,
    "data": {
        "month": 5,
        "year": 2026,
        "total_holidays": 2,
        "total_weekend_days": 8,
        "working_days": 20,
        "days_in_month": 31,
        "holidays_list": [
            {
                "id": 1,
                "date": "2026-05-01",
                "day_name": "Jumat",
                "note": "Hari Libur Nasional - Hari Buruh"
            },
            {
                "id": 5,
                "date": "2026-05-21",
                "day_name": "Kamis",
                "note": "Hari Libur Nasional - Kenaikan Isa Almasih"
            }
        ]
    },
    "message": "Ringkasan hari libur berhasil diambil"
}
```

---

## HTTP Status Codes

| Code | Meaning | Description |
|------|---------|-------------|
| 200 | OK | Berhasil (GET, PUT, DELETE) |
| 201 | Created | Resource berhasil dibuat (POST) |
| 207 | Multi-Status | Sebagian berhasil (bulk operations) |
| 400 | Bad Request | Error validasi umum |
| 404 | Not Found | Resource tidak ditemukan |
| 422 | Unprocessable Entity | Validation error |
| 500 | Server Error | Error di server |

---

## Usage Examples

### Example 1: Tambah Hari Libur Tunggal

```javascript
// JavaScript/Fetch API
const addHoliday = async () => {
    try {
        const response = await fetch('/sekretaris/api/holidays', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                date: '2026-05-21',
                note: 'Hari Libur Nasional - Kenaikan Isa Almasih'
            })
        });
        
        const data = await response.json();
        if (data.success) {
            console.log('Hari libur berhasil ditambahkan:', data.data);
        } else {
            console.error('Error:', data.message);
        }
    } catch (error) {
        console.error('Request failed:', error);
    }
};
```

### Example 2: Daftar Hari Libur Bulan Ini

```javascript
const getHolidays = async () => {
    const month = new Date().getMonth() + 1;
    const year = new Date().getFullYear();
    
    const response = await fetch(`/sekretaris/api/holidays/month/${month}/year/${year}`);
    const data = await response.json();
    
    if (data.success) {
        console.log('Hari libur bulan ini:', data.data);
        console.log('Total:', data.count);
    }
};
```

### Example 3: Tambah Banyak Hari Libur

```javascript
const bulkAddHolidays = async () => {
    const holidays = [
        { date: '2026-08-17', note: 'Hari Libur Nasional - HUT RI' },
        { date: '2026-12-25', note: 'Hari Libur Nasional - Natal' },
        { date: '2026-12-26', note: 'Cuti Bersama' }
    ];
    
    const response = await fetch('/sekretaris/api/holidays/bulk-add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ holidays })
    });
    
    const data = await response.json();
    console.log(`${data.created_count} berhasil, ${data.failed_count} gagal`);
    if (data.errors.length > 0) {
        console.log('Error:', data.errors);
    }
};
```

### Example 4: Update Hari Libur

```javascript
const updateHoliday = async (id) => {
    const response = await fetch(`/sekretaris/api/holidays/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            date: '2026-05-22',
            note: 'Hari Libur Dipindahkan'
        })
    });
    
    const data = await response.json();
    console.log(data.message);
};
```

### Example 5: Ringkasan Hari Libur

```javascript
const getHolidaysSummary = async () => {
    const response = await fetch('/sekretaris/api/holidays-summary?month=5&year=2026');
    const data = await response.json();
    
    if (data.success) {
        console.log(`Hari kerja: ${data.data.working_days}`);
        console.log(`Hari libur: ${data.data.total_holidays}`);
        console.log(`Weekend: ${data.data.total_weekend_days}`);
    }
};
```

---

## Integration dengan Sistem Absensi

Holiday API terintegrasi dengan sistem absensi sebagai berikut:

### 1. Otomatis Exclude Hari Libur dari Hitungan Kehadiran
Ketika menghitung statistik absensi, sistem otomatis mengecualikan hari libur yang terdaftar.

### 2. Penandaan Hari Libur di Tracker
Di halaman tracker absensi, hari libur ditampilkan dengan label khusus.

### 3. Verifikasi Saat Menandai Hadir Semua
Sistem akan menolak jika mencoba menandai semua hadir di hari libur.

### 4. Helper Methods di Model Holiday
```php
// Check apakah tanggal adalah hari libur
Holiday::isHoliday('2026-05-01'); // true/false

// Check apakah tanggal adalah hari kerja
Holiday::isWorkingDay('2026-05-01'); // true/false

// Get daftar hari libur bulan tertentu
Holiday::getHolidaysInMonth(5, 2026); // array of dates

// Hitung total hari libur
Holiday::countHolidaysInMonth(5, 2026); // integer
```

---

## Error Handling

### Validation Error
```json
{
    "success": false,
    "message": "Validasi gagal",
    "errors": {
        "date": ["Format tanggal harus Y-m-d"],
        "note": ["Keterangan harus diisi"]
    }
}
```

### Duplicate Holiday Error
```json
{
    "success": false,
    "message": "Tanggal 2026-05-01 sudah terdaftar sebagai hari libur: Hari Buruh"
}
```

### Not Found Error
```json
{
    "success": false,
    "message": "Hari libur tidak ditemukan"
}
```

### Server Error
```json
{
    "success": false,
    "message": "Gagal menambahkan hari libur: Error details"
}
```

---

## Best Practices

1. **Validasi Input**: Selalu validasi format tanggal (Y-m-d) sebelum mengirim request
2. **Handle Response**: Selalu cek field `success` untuk menentukan apakah request berhasil
3. **Paginasi**: Gunakan parameter `per_page` jika data besar
4. **Bulk Operation**: Gunakan endpoint bulk-add untuk menambah banyak hari libur sekaligus
5. **Error Handling**: Implement proper error handling dan user feedback
6. **Caching**: Cache daftar hari libur di client untuk performa lebih baik
7. **Timezone**: Pastikan timezone aplikasi sudah benar untuk consistency

---

## Troubleshooting

### Q: API mengembalikan 401 Unauthorized
**A:** Pastikan Anda sudah login dan memiliki role 'sekretaris'

### Q: Tidak bisa menambah hari libur di tanggal yang sudah ada
**A:** Sistem mencegah duplikasi. Gunakan endpoint update jika ingin mengubah tanggal yang sudah ada

### Q: Bulk add berhasil sebagian (error 207)
**A:** Check field `errors` di response untuk mengetahui item mana yang gagal

### Q: Response menunjukkan tanggal salah
**A:** Pastikan timezone database dan aplikasi sudah sinkron

---

## Changelog

### v1.0 (2026-05-21)
- Initial release
- GET /holidays - List with filters and pagination
- GET /holidays/month/{month}/year/{year} - Get by month/year
- POST /holidays - Create single holiday
- GET /holidays/{id} - Get detail holiday
- PUT /holidays/{id} - Update holiday
- DELETE /holidays/{id} - Delete holiday
- POST /holidays/bulk-add - Bulk create holidays
- GET /holidays-summary - Summary statistics

---

**Documentation Last Updated:** 2026-05-21
