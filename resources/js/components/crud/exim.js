
// read simpledatatable
import jsPDF from "jspdf";
import html2canvas from "html2canvas";

function exportTableToPDF(tableId, filename = 'report.pdf', title='Laporan') {
    const table = document.getElementById(tableId);
    if (!table) return;

    html2canvas(table, { scale: 2 }).then(canvas => {
        const imgData = canvas.toDataURL("image/png");
        const pdf = new jsPDF("p", "pt", "a4");

        const pageWidth = pdf.internal.pageSize.getWidth();
        const pageHeight = pdf.internal.pageSize.getHeight();


        const marginTop = 40;
        const marginLeft = 40;
        const marginRight = 40;
        const marginBottom = 40;

        const contentWidth = pageWidth - marginLeft - marginRight;
        const contentHeight = pageHeight - marginTop - marginBottom;

        const imgWidth = contentWidth;
        const imgHeight = canvas.height * imgWidth / canvas.width;

        let position = marginTop;
        let heightLeft = imgHeight;

        let page = 1;

        // page 1 saja
        pdf.setFontSize(20);
        pdf.text(title, marginLeft, 25);
        // pdf.setFontSize(10);
        // pdf.text(`Halaman ${page}`, pageWidth - marginRight - 60, 25);

        while (heightLeft > 0) {
            // Gambar tabel
            pdf.addImage(imgData, "PNG", marginLeft, position, imgWidth, imgHeight);

            heightLeft -= contentHeight + 70;
            position -= contentHeight + 70;
            page++;

            if (heightLeft > 0) pdf.addPage();
        }

        // pdf.save(filename);

        const pdfBlob = pdf.output('blob');
        const blobUrl = URL.createObjectURL(pdfBlob);
        window.open(blobUrl, '_blank');
    });
};


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


import * as XLSX from "xlsx";
import { saveAs } from "file-saver";

function exportTableToExcel(tableId, filename = 'report.xlsx') {
    const table = document.getElementById(tableId);
    const workbook = XLSX.utils.table_to_book(table, { sheet: "Sheet1" });
    const excelBuffer = XLSX.write(workbook, { bookType: "xlsx", type: "array" });
    const blob = new Blob([excelBuffer], { type: "application/octet-stream" });
    saveAs(blob, filename);
};

window.exim = {
    exportCSV: exportCSV,
    simpleDTtoCSV: simpleDTtoCSV,
    exportTableToPDF: exportTableToPDF,
    exportTableToExcel: exportTableToExcel,
}