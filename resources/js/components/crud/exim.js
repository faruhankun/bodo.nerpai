
// read simpledatatable
function simpleDTtoCSV(datatable) {
    let csv = [];

    // Ambil header
    let headers = [];
    datatable.data.headings.forEach(heading => {
        let text = heading.data.trim();
        text = '"' + text.replace(/"/g, '""') + '"';
        headers.push(text);
    });
    csv.push(headers.join(","));

    // Ambil semua data baris
    datatable.data.data.forEach(row => {
        let csvRow = row.cells.map(cell => {
            let text = cell.text.trim();
            return '"' + text.replace(/"/g, '""') + '"';
        });
        csv.push(csvRow.join(","));
    });

    return csv;
}

function exportCSV(csv, filename = "export.csv") {
    let csvFile = new Blob([csv], { type: "text/csv" });
    let downloadLink = document.createElement("a");

    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);

    downloadLink.click();
}

window.exim = {
    exportCSV: exportCSV,
    simpleDTtoCSV: simpleDTtoCSV
}