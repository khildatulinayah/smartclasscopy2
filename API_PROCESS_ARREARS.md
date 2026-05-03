# API Documentation - Process Arrears

## Endpoint

```
POST /bendahara/api/process-arrears
```

## Request Body

```json
{
    "student_id": 1,
    "transaction_id": 42,
    "month": 3,
    "year": 2025
}
```

### Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `student_id` | Integer | Yes | ID siswa (harus ada di tabel users) |
| `transaction_id` | Integer | Yes | ID transaksi kas (harus ada di tabel transactions) |
| `month` | Integer | Yes | Bulan (1-12) - **PENTING: Untuk membatasi lunasi ke bulan tertentu** |
| `year` | Integer | Yes | Tahun (2020-2030) - **PENTING: Untuk membatasi lunasi ke tahun tertentu** |

## Response

### Success Response (HTTP 200)
```json
{
    "success": true,
    "message": "Tunggakan berhasil dilunasi!",
    "count": 2
}
```

### Error Response (HTTP 422 / 404 / 500)
```json
{
    "success": false,
    "message": "Error message detail"
}
```

## JavaScript Example

### Basic Usage

```javascript
// Data untuk lunasi tunggakan
const arrearsData = {
    student_id: 1,
    transaction_id: 42,
    month: 3,
    year: 2025
};

// Call endpoint
fetch('/bendahara/api/process-arrears', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify(arrearsData)
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        alert(`✓ Tunggakan berhasil dilunasi! (${data.count} item)`);
        location.reload(); // Refresh halaman
    } else {
        alert('✗ Error: ' + data.message);
    }
})
.catch(error => {
    alert('✗ Gagal: ' + error.message);
});
```

### Complete Modal Example

```javascript
let arrearsModalData = {};

function showArrearsModal(studentId, studentName, totalAmount, unpaidWeeks) {
    // Set data
    arrearsModalData = {
        studentId: studentId,
        studentName: studentName,
        totalAmount: totalAmount,
        unpaidWeeks: unpaidWeeks
    };
    
    // Update modal content
    document.getElementById('arrears_student_id').value = studentId;
    document.getElementById('arrears_student_name').textContent = studentName;
    document.getElementById('arrears_total').textContent = 'Rp ' + totalAmount.toLocaleString('id-ID');
    document.getElementById('arrears_weeks').textContent = 'Minggu ' + unpaidWeeks;
    
    // Set current date
    document.getElementById('arrears_date').value = new Date().toISOString().split('T')[0];
    
    // Show modal
    document.getElementById('arrearsModal').classList.remove('hidden');
}

function closeArrearsModal() {
    document.getElementById('arrearsModal').classList.add('hidden');
    document.getElementById('arrearsForm').reset();
}

// Form submission
document.getElementById('arrearsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        student_id: parseInt(document.getElementById('arrears_student_id').value),
        transaction_id: parseInt(prompt('Masukkan ID Transaksi Kas:')), // atau ambil dari dropdown
        month: {{ $month }},      // Dari controller - current month
        year: {{ $year }},        // Dari controller - current year
    };
    
    if (!formData.transaction_id) {
        alert('Transaction ID diperlukan!');
        return;
    }
    
    // Show loading
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.textContent = 'MEMPROSES...';
    
    // Call API
    fetch('/bendahara/api/process-arrears', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`✓ ${data.count} tunggakan berhasil dilunasi!`);
            closeArrearsModal();
            location.reload();
        } else {
            alert('✗ Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('✗ Gagal: ' + error.message);
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Lunasi Sekarang';
    });
});
```

## Important Notes

### 🔴 Critical: Month & Year Parameters

**SEBELUM (BUGGY):**
```javascript
// Lunasi SEMUA tunggakan dari semua bulan!
fetch('/bendahara/api/process-arrears', {
    student_id: 1,
    transaction_id: 42
    // Tidak ada month & year
});
// Bug: Tunggakan bulan lain juga dilunasi
```

**SESUDAH (FIXED):**
```javascript
// Lunasi HANYA tunggakan bulan Maret 2025
fetch('/bendahara/api/process-arrears', {
    student_id: 1,
    transaction_id: 42,
    month: 3,      // ← PENTING
    year: 2025     // ← PENTING
});
// ✓ Hanya bulan Maret yang dilunasi
```

### Validasi di Controller

```php
$request->validate([
    'student_id' => 'required|exists:users,id',
    'transaction_id' => 'required|exists:transactions,id',
    'month' => 'required|integer|min:1|max:12',      // ← BARU
    'year' => 'required|integer|min:2020|max:2030'   // ← BARU
]);
```

Jika parameter month/year tidak lengkap, akan error 422.

### Business Logic

```php
// Hanya ambil unpaid payments untuk bulan & tahun tertentu
$unpaidPayments = WeeklyPayment::where('student_id', $request->student_id)
    ->where('month', $request->month)        // ← Filter bulan
    ->where('year', $request->year)          // ← Filter tahun
    ->where('status', 'unpaid')
    ->get();

// Lunasi hanya yang difilter
foreach ($unpaidPayments as $payment) {
    $payment->update([
        'status' => 'paid',
        'transaction_id' => $transaction->id,
        'payment_date' => $transaction->date,
    ]);
}
```

## Example Scenarios

### Scenario 1: Lunasi Tunggakan Mei

```javascript
const data = {
    student_id: 5,
    transaction_id: 100,
    month: 5,    // Mei
    year: 2025
};

fetch('/bendahara/api/process-arrears', {
    method: 'POST',
    headers: { 
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrfToken()
    },
    body: JSON.stringify(data)
})
.then(r => r.json())
.then(d => console.log(d.message)); // "Tunggakan berhasil dilunasi! (2 item)"
```

**Hasil:**
- ✅ Semua unpaid payments siswa 5 di Mei 2025 → status = paid
- ✅ Transaksi yang digunakan tercatat di payment

### Scenario 2: User Lupa Kasih Month/Year

```javascript
const data = {
    student_id: 5,
    transaction_id: 100
    // Lupa month & year!
};

fetch('/bendahara/api/process-arrears', { ... })
.then(r => r.json())
.then(d => console.log(d.message)); 
// Error 422: "The month field is required."
```

### Scenario 3: View Bulan Lain, Lunasi Bulan Tertentu

**User sedang view Bulan April 2025**, tapi ingin lunasi tunggakan **Maret 2025**:

```javascript
const currentMonth = {{ $month }};     // 4 (April)
const currentYear = {{ $year }};       // 2025

// Lunasi tunggakan Maret (custom selection)
const data = {
    student_id: 5,
    transaction_id: 100,
    month: 3,              // ← Lunasi Maret (bukan April yang sedang dilihat)
    year: 2025
};

fetch('/bendahara/api/process-arrears', { ... })
.then(r => r.json())
.then(d => {
    // Hanya tunggakan Maret yang dilunasi
    // Tunggakan April tetap unpaid
});
```

## Testing

### Unit Test
```php
// Test: Lunasi hanya bulan tertentu
$student = User::factory()->create(['role' => 'siswa']);
$transaction = Transaction::factory()->create(['type' => 'income']);

// Create unpaid payments di bulan 3 dan 4
WeeklyPayment::create([
    'student_id' => $student->id,
    'month' => 3, 'year' => 2025,
    'week_number' => 1,
    'status' => 'unpaid',
    'amount' => 5000
]);
WeeklyPayment::create([
    'student_id' => $student->id,
    'month' => 4, 'year' => 2025,
    'week_number' => 1,
    'status' => 'unpaid',
    'amount' => 5000
]);

// Process arrears hanya untuk bulan 3
$response = $this->post('/bendahara/api/process-arrears', [
    'student_id' => $student->id,
    'transaction_id' => $transaction->id,
    'month' => 3,
    'year' => 2025
]);

// Assertions
$response->assertJson(['success' => true, 'count' => 1]);
$this->assertDatabaseHas('weekly_payments', [
    'student_id' => $student->id,
    'month' => 3,
    'status' => 'paid'      // ✓ Paid
]);
$this->assertDatabaseHas('weekly_payments', [
    'student_id' => $student->id,
    'month' => 4,
    'status' => 'unpaid'    // ✓ Still unpaid
]);
```

