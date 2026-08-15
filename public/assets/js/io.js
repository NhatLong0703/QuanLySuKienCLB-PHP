// Helper for Export/Import CSV and Print PDF

/**
 * Xuất dữ liệu mảng Object thành file CSV (mở tốt trên Excel)
 * @param {Array} data - Dữ liệu dạng mảng object
 * @param {String} filename - Tên file tải xuống
 * @param {Array} headers - Mảng tiêu đề cột (tùy chọn)
 */
function exportToCSV(data, filename, headers = null) {
    if (!data || !data.length) {
        alert('Không có dữ liệu để xuất!');
        return;
    }

    let csvContent = "data:text/csv;charset=utf-8,\uFEFF"; // BOM for UTF-8 Excel support
    
    // Determine headers
    const keys = headers || Object.keys(data[0]);
    csvContent += keys.join(",") + "\r\n";

    // Data rows
    data.forEach(row => {
        let rowData = keys.map(k => {
            let val = row[k] === null || row[k] === undefined ? '' : row[k].toString();
            // Escape double quotes and enclose in quotes if contains comma
            val = val.replace(/"/g, '""');
            if (val.search(/("|,|\n)/g) >= 0) {
                val = `"${val}"`;
            }
            return val;
        });
        csvContent += rowData.join(",") + "\r\n";
    });

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", filename.endsWith('.csv') ? filename : filename + ".csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

/**
 * Đọc file CSV và chuyển thành mảng Object
 * @param {File} file - File CSV từ input
 * @param {Function} callback - Hàm gọi lại (callback) trả về dữ liệu JSON
 */
function importCSV(file, callback) {
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        const text = e.target.result;
        const lines = text.split(/\r\n|\n/);
        if (lines.length < 2) {
            alert('File CSV trống hoặc không đúng định dạng!');
            return;
        }

        const headers = lines[0].split(',').map(h => h.trim().replace(/^"|"$/g, ''));
        const result = [];
        
        for (let i = 1; i < lines.length; i++) {
            if (!lines[i].trim()) continue;
            
            // Xử lý regex để split đúng với các dấu phẩy nằm trong ngoặc kép
            const obj = {};
            let currentLine = lines[i];
            
            // Đơn giản hóa parser cho CSV cơ bản
            const values = currentLine.split(/,(?=(?:(?:[^"]*"){2})*[^"]*$)/);
            
            headers.forEach((header, index) => {
                let val = values[index] || '';
                val = val.trim().replace(/^"|"$/g, ''); // Bỏ ngoặc kép bọc ngoài
                obj[header] = val;
            });
            result.push(obj);
        }
        
        if (callback) callback(result);
    };
    reader.readAsText(file);
}

/**
 * Kích hoạt chức năng in trình duyệt để xuất PDF
 */
function exportToPDF() {
    window.print();
}
