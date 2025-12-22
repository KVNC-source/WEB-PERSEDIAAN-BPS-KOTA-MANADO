$(document).ready(function () {
  // 1. Initialize the Inventory Table (only if the table exists on the page)
  if ($("#atkTable").length) {
    const table = $("#atkTable").DataTable({
      order: [[0, "desc"]],
      pageLength: 25,
      dom: "Bfrtip",
      buttons: ["copy", "excel", "print"],
      language: {
        search: "Cari Data (Pegawai/Barang):",
        lengthMenu: "Tampilkan _MENU_ entri",
        paginate: {
          first: "Awal",
          last: "Akhir",
          next: "Lanjut",
          previous: "Balik",
        },
      },
      createdRow: function (row, data, dataIndex) {
        if (data[5] && data[5].includes("Tidak Disetujui")) {
          $(row).addClass("table-danger");
        }
      },
    });

    // Filter by Date Range
    $("#minDate, #maxDate").on("change", function () {
      table.draw();
    });
  }

  // 2. Form Validation for Uploads - FIXED TO CSV
  $("#uploadForm").on("submit", function (e) {
    let file = $("#fileInput").val();
    if (!file.toLowerCase().endsWith(".csv")) {
      alert("Harap unggah file format .csv sesuai standar sistem pemrosesan.");
      e.preventDefault();
    }
  });
});

// Logic for Automatic Print Trigger
function printForm(no_bukti) {
  // Corrected to PHP path based on your file structure
  window.location.href = `generate_pdf.php?no_bukti=${no_bukti}`;
}
