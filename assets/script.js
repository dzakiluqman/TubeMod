document.addEventListener("DOMContentLoaded", function() {

    // Validate search form
    const searchForm = document.querySelector('.search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const inputUrl = searchForm.querySelector('input[name="youtube_url"]').value;
            const submitBtn = searchForm.querySelector('button');
            
            if (inputUrl.trim() === '') {
                e.preventDefault();
                alert('YouTube URL cannot be empty!');
            } else if (!inputUrl.includes('youtube.com') && !inputUrl.includes('youtu.be')) {
                e.preventDefault();
                alert('Please enter a valid YouTube URL!');
            } else {
                submitBtn.innerHTML = 'Analyzing...';
                submitBtn.style.opacity = '0.7';
                submitBtn.style.cursor = 'wait';
            }
        });
    }

    // Confirm before deleting data (History, Keywords)
    const deleteLinks = document.querySelectorAll('.delete-text, .btn-danger');
    deleteLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            const confirmed = confirm("Are you sure you want to delete this data? This action cannot be undone.");
            
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });

    // Function to show the modal
    function openLogoutModal() {
        document.getElementById('logoutModal').style.display = 'block';
    }

    // Function to hide the modal
    function closeLogoutModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }

    // Close modal if user clicks anywhere outside the box
    window.onclick = function(event) {
        let modal = document.getElementById('logoutModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});