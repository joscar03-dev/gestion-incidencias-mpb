{
    dark: (localStorage.getItem('theme') ?? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')) === 'dark',
    showReportModal: false,
    reportForm: {
        titulo: '',
        descripcion: '',
        prioridad: 'Media'
    },
    showMobileMenu: false
}
