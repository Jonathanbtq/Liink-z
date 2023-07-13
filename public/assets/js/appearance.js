function validateImage(input) {
    var image = input.files[0];

    var imageType = /image.*/;
    if (!image.type.match(imageType)) {
        alert("Veuillez sélectionner une image valide.");
        input.value = ''; // Efface le fichier sélectionné
        return false;
    }

    var img = document.createElement("img");
    img.src = window.URL.createObjectURL(image);

    img.onload = function() {
        // Valide automatiquement le formulaire si l'image est valide
        input.closest('form').submit();
    }
}