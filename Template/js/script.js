$(document).ready(function () {
  // 1. Initialize the Inventory Table
  const table = $("#atkTable").DataTable({
    order: [[0, "desc"]], // Show newest No. Bukti first
    pageLength: 25,
    dom: "Bfrtip", // Enable export buttons
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
    // Highlight rows based on status
    createdRow: function (row, data, dataIndex) {
      if (data[5].includes("Tidak Disetujui")) {
        $(row).addClass("table-danger");
      }
    },
  });

  // 2. Custom Filter Logic for BPS Protocols
  // Filter by Date Range
  $("#minDate, #maxDate").on("change", function () {
    table.draw();
  });

  // 3. Form Validation for Uploads
  $("#uploadForm").on("submit", function (e) {
    let file = $("#fileInput").val();
    if (!file.endsWith(".xlsx")) {
      alert("Harap unggah file format .xlsx sesuai standar BPS");
      e.preventDefault();
    }
  });
});

// Logic for Automatic Print Trigger
function printForm(no_bukti) {
  // Redirect to the Python route that generates the PDF
  window.location.href = `/generate_pdf/${no_bukti}`;
}
