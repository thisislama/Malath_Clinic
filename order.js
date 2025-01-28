// Sort table rows by date and time on page load
window.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('appointmentTable');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.rows); // Get all rows as an array

    // Sort rows by date and time
    rows.sort((rowA, rowB) => {
        // Combine the date and time values from the respective columns
        const dateA = new Date(`${rowA.cells[0].textContent} ${rowA.cells[2].textContent}`);
        const dateB = new Date(`${rowB.cells[0].textContent} ${rowB.cells[2].textContent}`);

        // Return comparison for ascending order
        return dateA - dateB;
    });

    // Reinsert sorted rows into the table
    rows.forEach(row => tbody.appendChild(row));
});