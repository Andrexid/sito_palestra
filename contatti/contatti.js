document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("sendEmail");

  if (form) {
    form.addEventListener("submit", function (event) {
      event.preventDefault();
      alert("form inviato");
    });
  }
});
  