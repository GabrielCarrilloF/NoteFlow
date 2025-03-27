document.addEventListener("DOMContentLoaded", function () {
    loadNotes();
});

function loadNotes() {
    fetch("../controllers/notes.php")
        .then(response => response.json())
        .then(notes => {
            let notesList = document.getElementById("notesList");
            notesList.innerHTML = "";
            notes.forEach(note => {
                let noteItem = document.createElement("div");
                noteItem.classList.add("list-group-item");
                noteItem.innerHTML = `<strong>${note.Title}</strong><p>${note.Content}</p>`;
                notesList.appendChild(noteItem);
            });
        });
}

function showNoteModal() {
    let title = prompt("Enter note title:");
    let content = prompt("Enter note content:");
    if (title && content) {
        fetch("../controllers/notes.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `title=${title}&content=${content}`
        }).then(() => loadNotes());
    }
}
