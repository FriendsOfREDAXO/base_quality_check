
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.tasklink').forEach(function(link) {
        link.addEventListener('click', function(event) {

            var currentUrl = window.location.href;
          

            event.preventDefault();
            var action = this.getAttribute('data-action');
            var taskId = this.getAttribute('data-id');
            
            var xhr = new XMLHttpRequest();
            xhr.open('POST', currentUrl, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    // Handle the response here (e.g., update the task row)
                    location.reload(); // For simplicity, reload the page
                }
            };
            xhr.send('func=' + action + '&id=' + taskId);
        });
    });
});
