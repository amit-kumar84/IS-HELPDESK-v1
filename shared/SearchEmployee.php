

<?php
// Preload ALL employee data on page load for instant filtering
$all_employees = [];
$emp_query = "SELECT * FROM `emp_details` ORDER BY `cost_center`, `deptt`, `sec`, `staffid`, `username` ASC";
$emp_result = mysqli_query($link, $emp_query);

while($emp = mysqli_fetch_assoc($emp_result)) {
    $emp['hardware'] = [];
    $staff_id = $emp['staffid'];
    
    // Fetch all hardware in one query
    $hw_query = "SELECT CATG, HD_ID_NO, MAKE, MODEL FROM `hardware_master` WHERE `STAFF_NO`='$staff_id'
                UNION ALL
                SELECT CATG, HD_ID_NO, MAKE, HD_ID_RECORD as MODEL FROM `hardware_stroage_master` WHERE `STAFF_NO`='$staff_id'";
    
    $hw_result = mysqli_query($link, $hw_query);
    while($hw = mysqli_fetch_assoc($hw_result)) {
        $emp['hardware'][] = $hw;
    }
    
    $all_employees[] = $emp;
}

// Convert to JSON for JavaScript filtering
$employees_json = json_encode($all_employees);
?>

<style>
* {
    transition: all 0.3s ease;
}

.search-container {
    margin-bottom: 30px;
    animation: slideInDown 0.6s ease;
}

.search-controls {
    background: linear-gradient(135deg, rgba(255,153,51,0.1) 0%, rgba(10,31,68,0.05) 100%);
    padding: 20px;
    border-radius: 14px;
    border: 2px solid rgba(255,153,51,0.3);
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.search-input {
    flex: 1;
    min-width: 280px;
    padding: 12px 16px;
    font-size: 14px;
    border: 2px solid #e5edf6;
    border-radius: 10px;
    font-family: 'Segoe UI', Arial, sans-serif;
    background: white;
    color: #0a1f44;
    font-weight: 500;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

.search-input:focus {
    outline: none;
    border-color: #ff9933;
    box-shadow: 0 0 0 4px rgba(255,153,51,0.15), 0 4px 12px rgba(255,153,51,0.25);
    transform: translateY(-2px);
}

.search-input::placeholder {
    color: #94a3b8;
}

.loading-spinner {
    display: inline-block;
    margin-left: 0;
    color: #ff9933;
    font-weight: 700;
    font-size: 14px;
    letter-spacing: 0.5px;
}

.result-count {
    margin-left: auto;
    background: linear-gradient(135deg, #138808 0%, #16a34a 100%);
    color: white;
    font-size: 13px;
    font-weight: 700;
    padding: 8px 16px;
    border-radius: 20px;
    box-shadow: 0 4px 12px rgba(19, 136, 8, 0.3);
    letter-spacing: 0.5px;
}

.table-wrapper {
    background: white;
    border-radius: 14px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
    overflow: hidden;
    animation: slideInUp 0.7s ease;
}

#table_func {
    width: 100%;
    border-collapse: collapse;
    background: white;
    font-family: 'Segoe UI', Arial, sans-serif;
}

#table_func thead tr {
    background: linear-gradient(135deg, #ff9933 0%, #ea7600 100%);
}

#table_func thead th {
    padding: 14px 12px;
    text-align: left;
    color: white;
    font-weight: 700;
    font-size: 12px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    border: 1px solid rgba(0,0,0,0.1);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.3);
}

#table_func tbody tr {
    border-bottom: 1px solid #e5edf6;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

#table_func tbody tr:hover {
    background: linear-gradient(135deg, rgba(255,153,51,0.12) 0%, rgba(255,153,51,0.05) 100%);
    box-shadow: inset 0 0 0 2px rgba(255,153,51,0.2);
    transform: scale(1.01);
}

#table_func tbody tr:last-child {
    border-bottom: 2px solid #ff9933;
}

#table_func tbody td {
    padding: 12px 12px;
    color: #0a1f44;
    font-size: 13px;
    border: 1px solid #e5edf6;
    line-height: 1.5;
}

#table_func tbody tr td:first-child {
    text-align: center;
    font-weight: 700;
    color: #ff9933;
    background: linear-gradient(135deg, rgba(255,153,51,0.05) 0%, transparent 100%);
}

#table_func tbody img {
    border-radius: 8px;
    border: 2px solid #e5edf6;
    transition: all 0.25s ease;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

#table_func tbody img:hover {
    border-color: #ff9933;
    box-shadow: 0 4px 12px rgba(255,153,51,0.3);
    transform: scale(1.05);
}

#table_func tbody tr td:nth-child(2),
#table_func tbody tr td:nth-child(3) {
    font-weight: 600;
    color: #0a1f44;
}

#table_func tbody tr td:nth-child(4) {
    font-weight: 700;
    color: #0a1f44;
    background: linear-gradient(135deg, rgba(19,136,8,0.08) 0%, transparent 100%);
}

.btn-print {
    padding: 11px 20px;
    background: linear-gradient(135deg, #0891b2 0%, #06b6d4 50%, #0284c7 100%);
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 700;
    font-size: 13px;
    letter-spacing: 0.5px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 12px rgba(8,145,178,0.3);
    display: flex;
    align-items: center;
    gap: 6px;
    text-transform: uppercase;
    white-space: nowrap;
}

.btn-print:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(8,145,178,0.4);
    background: linear-gradient(135deg, #0891b2 0%, #06b6d4 50%, #0369a1 100%);
}

.btn-print:active {
    transform: translateY(-1px);
}

.no-data-message {
    padding: 40px 20px;
    text-align: center;
    color: #94a3b8;
    font-size: 15px;
    background: linear-gradient(135deg, rgba(255,153,51,0.05) 0%, transparent 100%);
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Mobile responsive */
@media (max-width: 768px) {
    .search-controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .search-input {
        width: 100%;
        min-width: unset;
    }
    
    .result-count {
        margin-left: 0;
        text-align: center;
    }
    
    .btn-print {
        justify-content: center;
    }
    
    #table_func tbody td {
        padding: 10px 8px;
        font-size: 12px;
    }
    
    #table_func thead th {
        padding: 10px 8px;
        font-size: 11px;
    }
}
</style>
<div class="search-container">
<div class="search-controls">
    <input type="text" id="searchInput" class="search-input" autofocus placeholder="🔍 Search by Staff Number or Employee Name...">
    <span class="loading-spinner" id="loadingStatus"></span>
    <div class="result-count">📊 Total: <span id="resultCount">0</span> Employees</div>
    <button onclick="printTable()" class="btn-print">🖨️ Print Results</button>
</div>

<div class="table-wrapper">
    <table id="table_func">
        <thead>
            <tr>
                <th>#</th>
                <th>Cost Center</th>
                <th>Department (Section)</th>
                <th>Staff ID</th>
                <th>Photo</th>
                <th>Employee Name</th>
                <th>Designation</th>
                <th>Phone</th>
                <th>IP Phone</th>
                <th>Assigned Assets</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <!-- Data will be inserted by JavaScript -->
        </tbody>
    </table>
</div>
</div>

<script>
const employeesData = <?php echo $employees_json; ?>;
let filteredEmployees = [...employeesData];

function renderTable(data) {
    const tbody = document.getElementById('tableBody');
    tbody.innerHTML = '';
    
    if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="10" class="no-data-message">
            <div style="font-size: 48px; margin-bottom: 10px;">🔍</div>
            <strong style="color: #0a1f44;">No employees found</strong><br>
            <span style="font-size: 12px;">Try searching with a different name or staff number</span>
        </td></tr>`;
        document.getElementById('resultCount').textContent = '0';
        return;
    }
    
    data.forEach((emp, index) => {
        const hardwareHtml = emp.hardware.length > 0 
            ? emp.hardware.map(hw => {
                const colors = {
                    'PC': '#2563eb',
                    'Laptop': '#7c3aed',
                    'VDI': '#dc2626',
                    'PRINTER': '#ea580c',
                    'WEB_CAM': '#0891b2',
                    'NETWORK': '#16a34a'
                };
                const color = colors[hw.CATG] || '#64748b';
                return `<span style="background: ${color}; color: white; padding: 3px 8px; border-radius: 4px; display: inline-block; margin: 2px 2px; font-size: 11px; font-weight: 600;">📦 ${hw.CATG}: ${hw.HD_ID_NO}</span>`;
            }).join('')
            : '<span style="color: #94a3b8; font-style: italic;">No assets assigned</span>';
        
        const ipPhone = emp.ip_phone && emp.ip_phone !== '0' ? emp.ip_phone : '—';
        
        // Default avatar for missing photos
        const defaultAvatar = 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Cdefs%3E%3ClinearGradient id=%22bg%22 x1=%220%25%22 y1=%220%25%22 x2=%22100%25%22 y2=%22100%25%22%3E%3Cstop offset=%220%25%22 style=%22stop-color:%230891b2;stop-opacity:1%22 /%3E%3Cstop offset=%22100%25%22 style=%22stop-color:%2306b6d4;stop-opacity:1%22 /%3E%3C/linearGradient%3E%3C/defs%3E%3Ccircle cx=%2250%22 cy=%2250%22 r=%2250%22 fill=%22url(%23bg)%22 /%3E%3Ccircle cx=%2250%22 cy=%2240%22 r=%2218%22 fill=%22%23fff%22 /%3E%3Cpath d=%22M 25 65 Q 25 55 50 55 Q 75 55 75 65 L 75 85 Q 75 90 70 90 L 30 90 Q 25 90 25 85 Z%22 fill=%22%23fff%22 /%3E%3C/svg%3E';
        
        const row = `
            <tr style="animation: fadeIn 0.3s ease ${index * 0.05}s backwards;">
                <td style="font-weight: 700; background: linear-gradient(135deg, rgba(255,153,51,0.1) 0%, transparent 100%);">${index + 1}</td>
                <td style="font-weight: 600;">${emp.cost_center || '—'}</td>
                <td style="font-weight: 600;"><span style="background: rgba(255,153,51,0.15); padding: 4px 8px; border-radius: 4px;">${emp.deptt} (${emp.sec})</span></td>
                <td style="font-weight: 700; color: #0a1f44;">${emp.staffid}</td>
                <td style="text-align: center;"><img src="Pictures/${emp.staffid}.JPG" alt="Photo" height="56px" width="48px" style="border-radius: 8px; border: 2px solid #e5edf6; object-fit: cover;" onerror="this.src='${defaultAvatar}'" /></td>
                <td style="font-weight: 600; color: #0a1f44;">${emp.username || '—'}</td>
                <td style="color: #475569;">${emp.desg || '—'}</td>
                <td style="font-family: 'Courier New', monospace; color: #0a1f44; font-weight: 600;">${emp.phone_no || '—'}</td>
                <td style="font-family: 'Courier New', monospace; color: #0a1f44; font-weight: 600;">${ipPhone}</td>
                <td style="text-align: left; min-width: 300px;">${hardwareHtml}</td>
            </tr>
        `;
        tbody.innerHTML += row;
    });
    
    document.getElementById('resultCount').textContent = data.length;
}

// Instant search on key press
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase().trim();
    
    if (searchTerm === '') {
        filteredEmployees = [...employeesData];
    } else {
        filteredEmployees = employeesData.filter(emp => 
            emp.staffid.toLowerCase().includes(searchTerm) ||
            (emp.username && emp.username.toLowerCase().includes(searchTerm))
        );
    }
    
    renderTable(filteredEmployees);
});

// Print function
function printTable() {
    const divText = document.getElementById("table_func").outerHTML;
    const myWindow = window.open('', '', 'width=1400,height=900');
    const doc = myWindow.document;
    doc.open();
    doc.write(`<html><head><title>Employee Search Results</title><style>
        body { font-family: 'Segoe UI', Arial; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: linear-gradient(135deg, #ff9933, #ea7600); color: white; padding: 12px; text-align: left; font-weight: bold; font-size: 11px; }
        td { border: 1px solid #ddd; padding: 10px; font-size: 12px; }
        tr:nth-child(even) { background: rgba(255,153,51,0.05); }
        tr:hover { background: rgba(255,153,51,0.1); }
        h2 { color: #0a1f44; margin-bottom: 5px; }
        .timestamp { color: #666; font-size: 12px; }
    </style></head><body>
        <h2>📋 Employee Directory Report</h2>
        <div class="timestamp">Generated on: ${new Date().toLocaleString()}</div>
    `);
    doc.write(divText);
    doc.write('</body></html>');
    doc.close();
    setTimeout(() => myWindow.print(), 800);
}

// Add fade-in animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
`;
document.head.appendChild(style);

// Load table on page load
document.addEventListener('DOMContentLoaded', function() {
    renderTable(employeesData);
    document.getElementById('searchInput').focus();
});
</script>

<style>
#table_func {
    width: 100%;
    border-collapse: collapse;
    background: white;
    font-family: 'Segoe UI', Arial, sans-serif;
}

#table_func thead tr {
    background: linear-gradient(135deg, #ff9933 0%, #ea7600 100%);
}

#table_func thead th {
    padding: 14px 12px;
    text-align: left;
    color: white;
    font-weight: 700;
    font-size: 12px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    border: 1px solid rgba(0,0,0,0.1);
    box-shadow: inset 0 1px 0 rgba(255,255,255,0.3);
}

#table_func tbody tr {
    border-bottom: 1px solid #e5edf6;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

#table_func tbody tr:hover {
    background: linear-gradient(135deg, rgba(255,153,51,0.12) 0%, rgba(255,153,51,0.05) 100%);
    box-shadow: inset 0 0 0 2px rgba(255,153,51,0.2);
    transform: scale(1.01);
}

#table_func tbody tr:last-child {
    border-bottom: 2px solid #ff9933;
}

#table_func tbody td {
    padding: 12px 12px;
    color: #0a1f44;
    font-size: 13px;
    border: 1px solid #e5edf6;
    line-height: 1.5;
}

#table_func tbody img {
    border-radius: 8px;
    border: 2px solid #e5edf6;
    transition: all 0.25s ease;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

#table_func tbody img:hover {
    border-color: #ff9933;
    box-shadow: 0 4px 12px rgba(255,153,51,0.3);
    transform: scale(1.05);
}

.no-data-message {
    padding: 40px 20px;
    text-align: center;
    background: linear-gradient(135deg, rgba(255,153,51,0.05) 0%, transparent 100%);
}
</style>