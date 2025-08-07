<script>
    const toggleBtn = document.getElementById('menuToggle');
    const menuIcon = document.getElementById('menuIcon');


    toggleBtn.addEventListener('click', () => {

        menuIcon.classList.toggle('fa-bars');
        menuIcon.classList.toggle('fa-xmark');
    });
</script>


</body>

</html>