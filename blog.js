document.addEventListener("DOMContentLoaded", () => {

    const registerBtn = document.getElementById("register-btn");
    const modal = document.getElementById("registration-modal");
    const closeBtn = document.querySelector(".close-button");
    const registrationForm = document.getElementById("registration-form");
    const usernameDisplay = document.getElementById("username-display");

    registerBtn.addEventListener("click", () => {
        modal.style.display = "flex";
    });

    closeBtn.addEventListener("click", () => {
        modal.style.display = "none";
    });

    registrationForm.addEventListener("submit", (event) => {
        event.preventDefault();

        const username = document.getElementById("username").value;

        usernameDisplay.textContent = `Welcome, ${username}`;
        usernameDisplay.style.display = "inline";
        registerBtn.style.display = "none";

        document.getElementById("username-input").value = username;
        document.getElementById("email-input").value = document.getElementById("email").value;

        modal.style.display = "none";
    });

    window.addEventListener("click", (event) => {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });

    const form = document.getElementById("post-form");
    const commentsList = document.querySelector(".comments-list ul");

    function loadPosts() {
        fetch("post_handler.php", {
            method: "POST",
            body: formData,
        })
        .then((response) => response.text())
        .then((message) => {
            if (message === "Post submitted successfully!") {
                loadPosts();
                form.reset();
            } else {
                alert("Error: " + message);
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("An error occurred.");
        });
        
    }
    form.addEventListener("submit", (event) => {
        event.preventDefault();
        console.log("Post button clicked");
        const formData = new FormData(form);
        formData.append("username", document.getElementById("username").value);
        formData.append("email", document.getElementById("email").value);
    
        fetch("post_handler.php", {
            method: "POST",
            body: formData,
        })
        .then((response) => response.text())
        .then((message) => {
            alert(message);
            loadPosts(); 
            form.reset();
        });
    });
    

    loadPosts(); 
});
