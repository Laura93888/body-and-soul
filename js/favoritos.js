document.addEventListener("click", function(e){

  const btn = e.target.closest(".activity-favorite-btn");

  if(!btn) return;

  e.preventDefault();

  const url = btn.dataset.url;

  fetch(url)
    .then(res => res.json())
    .then(data => {

      if(data.success){
        btn.classList.toggle("activo", data.esFavorito);
        btn.innerHTML = data.esFavorito ? "❤️" : "🤍";

        if(btn.classList.contains("favorite-icon-btn") && !data.esFavorito){
          btn.closest(".favorite-card").remove();
        }
      }

    })
    .catch(error => console.log("Error favoritos:", error));

});