// Pesquisa de unidades
document.getElementById("searchInput").addEventListener("keyup", function () {
  let filter = this.value.toLowerCase();
  let cards = document.getElementsByClassName("unidade-card");

  for (let i = 0; i < cards.length; i++) {
    let title = cards[i].querySelector("h3").innerText.toLowerCase();
    let endereco = cards[i].querySelector(".unidade-endereco").innerText.toLowerCase();

    if (title.includes(filter) || endereco.includes(filter)) {
      cards[i].style.display = "";
    } else {
      cards[i].style.display = "none";
    }
  }
});
