import EditorJS from '@editorjs/editorjs';

window.EditorJS = EditorJS;

// SIMPEL Application Scripts
document.addEventListener('DOMContentLoaded', () => {
    // Sidebar scroll containment and redirection
    const sidebar = document.querySelector('.sidebar');
    const menuArea = document.querySelector('.sidebar-menu-area');

    if (sidebar && menuArea) {
        sidebar.addEventListener('wheel', (e) => {
            menuArea.scrollTop += e.deltaY;
            e.preventDefault();
        }, { passive: false });
    }
});
