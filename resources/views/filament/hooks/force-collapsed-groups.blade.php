<script>
    (function () {
        try {
            // Force these groups to be collapsed on every load (admin panel only).
            var forced = ['User Management', 'Room Management', 'Booking & Reservation']
            localStorage.setItem('collapsedGroups', JSON.stringify(forced))
        } catch (e) {
            // ignore
        }
    })()
</script>

