// Sort table rows by date and time on page load
window.addEventListener('DOMContentLoaded', () => {
    const table = document.getElementById('appointmentTable');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.rows); // Get all rows as an array

    // Sort rows using toSorted()
    const sortedRows = rows.toSorted((rowA, rowB) => {
        // Combine date and time columns into Date objects
        const dateA = new Date(`${rowA.cells[0].textContent} ${rowA.cells[2].textContent}`);
        const dateB = new Date(`${rowB.cells[0].textContent} ${rowB.cells[2].textContent}`);

        // Return comparison for ascending order
        return dateA - dateB;
    });

    // Reinsert the sorted rows into the table
    sortedRows.forEach(row => tbody.appendChild(row));
});
