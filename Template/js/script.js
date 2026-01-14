$(document).ready(function () {
  const handleUpload = (formId, type) => {
    $(`#${formId}`).on("submit", function (e) {
      e.preventDefault();
      const fileInput = $(this).find('input[type="file"]')[0];
      const file = fileInput.files[0];
      const reader = new FileReader();

      reader.onload = function (e) {
        const data = new Uint8Array(e.target.result);
        // cellDates: true ensures SheetJS converts Excel date serials to JS Date objects
        const workbook = XLSX.read(data, { type: "array", cellDates: true });
        const firstSheet = workbook.Sheets[workbook.SheetNames[0]];

        // Convert to Array of Arrays
        let rows = XLSX.utils.sheet_to_json(firstSheet, {
          header: 1,
          blankrows: false,
        });

        // Define skip lines (Pemasukan: 3, Master: 4, Pengeluaran: 1)
        let skip = type === "pemasukan" ? 3 : type === "master" ? 4 : 1;
        let lastValidDate = "";
        let processedData = [];

        rows.forEach((row, index) => {
          if (index < skip) return;

          // Skip truly empty rows (ignore multiple commas/blank cells)
          if (
            row.filter((cell) => cell !== null && String(cell).trim() !== "")
              .length === 0
          )
            return;

          // Carry-forward Logic for Date (Index 0)
          if (row[0]) {
            if (row[0] instanceof Date) {
              // Convert JS Date to MySQL YYYY-MM-DD
              lastValidDate = row[0].toISOString().split("T")[0];
            } else {
              lastValidDate = row[0];
            }
          }
          row[0] = lastValidDate;

          processedData.push(row);
        });

        // Post processed JSON to PHP
        $.ajax({
          url: "../DB/process_upload_json.php",
          method: "POST",
          data: { type: type, payload: JSON.stringify(processedData) },
          success: function (response) {
            const res = JSON.parse(response);
            alert(`Sukses! ${res.success} data telah diproses.`);
            window.location.href = "index.php";
          },
          error: function () {
            alert("Gagal menghubungi server.");
          },
        });
      };
      reader.readAsArrayBuffer(file);
    });
  };

  if ($("#form-pengeluaran").length) {
    handleUpload("form-pengeluaran", "pengeluaran");
    handleUpload("form-pemasukan", "pemasukan");
    handleUpload("form-master", "master");
  }
});
