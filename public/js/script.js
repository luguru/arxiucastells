// --- DATOS INICIALES Y CONFIGURACIÓN ---
const categorias = { 1: '', 2: 'LIBROS', 3: 'DOCUMENTOS', 4: 'JOYAS', 5: 'MUSICA CLÁSICA', 6: 'ÓPERA', 7: 'POP', 8: 'CINE', 9: 'OTROS' };
const generos = { 1: '', 2: 'ARTE', 3: 'ANTROPOLOGÍA', 4: 'ARQUITECTURA', 5: 'BIOGRAFÍA', 6: 'BIOGRAFÍA NOVELADA', 7: 'CINE', 8: 'COCINA', 9: 'ENSAYO', 10: 'HISTORIA', 11: 'HISTORIA NOVELADA', 12: 'MÚSICA', 13: 'NARRATIVA', 14: 'POESÍA', 15: 'RELIGIÓN', 16: 'TEMAS COFRADES', 17: 'VARIOS', 18: 'VIAJES' };

// Paleta de colores para etiquetas
const categoryColors = {
    'LIBROS': 'bg-blue-100 text-blue-800',
    'DOCUMENTOS': 'bg-indigo-100 text-indigo-800',
    'JOYAS': 'bg-purple-100 text-purple-800',
    'MUSICA CLÁSICA': 'bg-pink-100 text-pink-800',
    'ÓPERA': 'bg-red-100 text-red-800',
    'POP': 'bg-yellow-100 text-yellow-800',
    'CINE': 'bg-orange-100 text-orange-800',
    'OTROS': 'bg-gray-200 text-gray-800',
};

const genreColors = {
    'ARTE': 'bg-green-100 text-green-800',
    'ANTROPOLOGÍA': 'bg-teal-100 text-teal-800',
    'ARQUITECTURA': 'bg-cyan-100 text-cyan-800',
    'BIOGRAFÍA': 'bg-sky-100 text-sky-800',
    'BIOGRAFÍA NOVELADA': 'bg-blue-100 text-blue-800',
    'CINE': 'bg-orange-100 text-orange-800',
    'COCINA': 'bg-amber-100 text-amber-800',
    'ENSAYO': 'bg-lime-100 text-lime-800',
    'HISTORIA': 'bg-yellow-100 text-yellow-800',
    'HISTORIA NOVELADA': 'bg-amber-100 text-amber-800',
    'MÚSICA': 'bg-pink-100 text-pink-800',
    'NARRATIVA': 'bg-indigo-100 text-indigo-800',
    'POESÍA': 'bg-purple-100 text-purple-800',
    'RELIGIÓN': 'bg-fuchsia-100 text-fuchsia-800',
    'TEMAS COFRADES': 'bg-rose-100 text-rose-800',
    'VARIOS': 'bg-gray-200 text-gray-800',
    'VIAJES': 'bg-emerald-100 text-emerald-800',
};


let libraryData = []; 
let currentSort = { key: 'id', order: 'desc' };
let currentPage = 1;
let totalPages = 1;
let recordsPerPage = 10;
let currentSearchParams = {};
let fetchedCoverUrl = null;
const API_URL = '../src/api/index.php'; // Ruta correcta a la API
const PLACEHOLDER_IMG = 'https://placehold.co/80x120/EFEFEF/AAAAAA?text=N/A';

// --- LÓGICA DE LA APLICACIÓN ---

document.addEventListener('DOMContentLoaded', () => {
    populateSelects();
    addEventListeners();
    loadBooks(1);
});

function addEventListeners() {
    document.getElementById('save-btn').addEventListener('click', () => handleSave(false));
    document.getElementById('search-btn').addEventListener('click', handleSearch);
    document.getElementById('show-all-btn').addEventListener('click', showAll);
    document.getElementById('clear-btn').addEventListener('click', clearForm);
    document.getElementById('update-btn').addEventListener('click', () => handleSave(true));
    document.getElementById('delete-btn').addEventListener('click', handleDelete);
    document.getElementById('close-modal-btn').addEventListener('click', closeModal);
    document.getElementById('cancel-btn').addEventListener('click', closeModal);
    document.getElementById('isbn-search-btn').addEventListener('click', fetchBookByISBN);
    document.getElementById('per-page-select').addEventListener('change', handlePerPageChange);

    document.querySelectorAll('.table-sortable th[data-sort]').forEach(header => {
        header.addEventListener('click', () => {
            const key = header.dataset.sort;
            document.querySelectorAll('.table-sortable th[data-sort]').forEach(h => {
                if (h !== header) h.classList.remove('sort-asc', 'sort-desc');
            });

            if (currentSort.key === key) {
                currentSort.order = currentSort.order === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort.key = key;
                currentSort.order = 'asc';
            }
             header.classList.remove('sort-asc', 'sort-desc');
             header.classList.add(currentSort.order === 'asc' ? 'sort-asc' : 'sort-desc');
            loadBooks(1);
        });
    });
}

function handlePerPageChange(event) {
    recordsPerPage = parseInt(event.target.value, 10);
    loadBooks(1); // Recargar desde la primera página
}

async function fetchBookByISBN() {
    const isbn = document.getElementById('isbn').value.trim();
    if (!isbn) {
        showToast('Por favor, introduce un ISBN.', 'error');
        return;
    }
    const searchIcon = document.getElementById('isbn-search-icon');
    const spinner = document.getElementById('isbn-spinner');
    searchIcon.classList.add('hidden');
    spinner.classList.remove('hidden');
    try {
        const response = await fetch(`https://www.googleapis.com/books/v1/volumes?q=isbn:${isbn}`);
        if (!response.ok) throw new Error('Error en la respuesta de la API de Google Books.');
        const data = await response.json();
        if (data.totalItems === 0) {
            showToast('No se encontró ningún libro con ese ISBN.', 'error');
            return;
        }
        const book = data.items[0].volumeInfo;
        document.getElementById('titulo').value = book.title || '';
        document.getElementById('autor').value = book.authors ? book.authors.join(', ') : '';
        document.getElementById('editorial').value = book.publisher || '';
        if (book.publishedDate) {
            const year = book.publishedDate.split('-')[0];
            document.getElementById('edicion').value = year;
        }
        const previewContainer = document.getElementById('portada-preview-container');
        const previewImg = document.getElementById('portada-preview-img');
        if (book.imageLinks && book.imageLinks.thumbnail) {
            fetchedCoverUrl = book.imageLinks.thumbnail.replace("http://", "https://");
            previewImg.src = fetchedCoverUrl;
            previewContainer.classList.remove('hidden');
        } else {
            fetchedCoverUrl = null;
            previewContainer.classList.add('hidden');
        }
        showToast('Información del libro cargada.', 'success');
    } catch (error) {
        console.error('Error al buscar por ISBN:', error);
        showToast('No se pudo obtener la información del libro.', 'error');
    } finally {
        searchIcon.classList.remove('hidden');
        spinner.classList.add('hidden');
    }
}

async function loadBooks(page = 1) {
    const tableBody = document.getElementById('results-body');
    tableBody.innerHTML = `<tr><td colspan="8" class="text-center p-8"><div class="loader mx-auto"></div><p class="mt-2 text-gray-500">Cargando datos...</p></td></tr>`;
    try {
        const params = new URLSearchParams({
            action: 'read',
            page: page,
            limit: recordsPerPage,
            sort_by: currentSort.key,
            sort_order: currentSort.order,
            ...currentSearchParams
        });
        const response = await fetch(`${API_URL}?${params}`);
        
        if (response.status === 401) {
             showToast('Su sesión ha expirado. Será redirigido al login.', 'error');
             setTimeout(() => window.location.href = 'login.php', 2000);
             return;
        }

        if (!response.ok) {
            const errorData = await response.json();
            throw new Error(errorData.details || `HTTP error! status: ${response.status}`);
        }
        const result = await response.json();
        libraryData = result.data;
        currentPage = result.current_page;
        totalPages = result.total_pages;
        renderTable(libraryData);
        renderPagination();
        updateRecordCount(result.total_records);
    } catch (error) {
        console.error('Error al cargar los libros:', error);
        showToast('Error al cargar los libros.', 'error');
        tableBody.innerHTML = `<tr><td colspan="8" class="text-center p-4 text-red-500">No se pudieron cargar los datos. Revisa la conexión con el servidor.</td></tr>`;
    }
}

function renderPagination() {
    const controls = document.getElementById('pagination-controls');
    controls.innerHTML = '';
    if (totalPages <= 1) return;

    controls.innerHTML += `<button onclick="changePage(${currentPage - 1})" class="px-3 py-1.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-md hover:bg-gray-100 ${currentPage === 1 ? 'opacity-50 cursor-not-allowed' : ''}" ${currentPage === 1 ? 'disabled' : ''}>&laquo; Ant</button>`;
    
    let pagesHtml = '';
    const maxPagesToShow = 7;
    let startPage, endPage;

    if (totalPages <= maxPagesToShow) {
        startPage = 1;
        endPage = totalPages;
    } else {
        const maxPagesBeforeCurrent = Math.floor((maxPagesToShow - 3) / 2);
        const maxPagesAfterCurrent = Math.ceil((maxPagesToShow - 3) / 2);
        if (currentPage <= maxPagesBeforeCurrent + 1) {
            startPage = 1;
            endPage = maxPagesToShow - 2;
        } else if (currentPage + maxPagesAfterCurrent >= totalPages) {
            startPage = totalPages - (maxPagesToShow - 3);
            endPage = totalPages;
        } else {
            startPage = currentPage - maxPagesBeforeCurrent;
            endPage = currentPage + maxPagesAfterCurrent;
        }
    }
    
    if (startPage > 1) {
        pagesHtml += `<button onclick="changePage(1)" class="px-3 py-1.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-md hover:bg-gray-100">1</button>`;
        if (startPage > 2) {
             pagesHtml += `<span class="px-3 py-1.5 text-sm font-medium text-gray-600">...</span>`;
        }
    }

    for (let i = startPage; i <= endPage; i++) {
         pagesHtml += `<button onclick="changePage(${i})" class="px-3 py-1.5 text-sm font-medium border rounded-md ${i === currentPage ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-100'}">${i}</button>`;
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            pagesHtml += `<span class="px-3 py-1.5 text-sm font-medium text-gray-600">...</span>`;
        }
        pagesHtml += `<button onclick="changePage(${totalPages})" class="px-3 py-1.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-md hover:bg-gray-100">${totalPages}</button>`;
    }
    
    controls.innerHTML += pagesHtml;

    controls.innerHTML += `<button onclick="changePage(${currentPage + 1})" class="px-3 py-1.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-md hover:bg-gray-100 ${currentPage === totalPages ? 'opacity-50 cursor-not-allowed' : ''}" ${currentPage === totalPages ? 'disabled' : ''}>Sig &raquo;</button>`;
}


window.changePage = function(page) {
    if (page > 0 && page <= totalPages) {
        loadBooks(page);
    }
}

function updateRecordCount(total) {
    const recordCount = document.getElementById('record-count');
    recordCount.textContent = `${total} registros encontrados`;
}

function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    toastMessage.textContent = message;
    
    toast.classList.remove('bg-green-500', 'bg-red-500');
    if (type === 'success') {
        toast.classList.add('bg-green-500');
    } else {
        toast.classList.add('bg-red-500');
    }

    toast.classList.remove('opacity-0');
    setTimeout(() => toast.classList.add('opacity-0'), 3000);
}


function populateSelects() {
    const categoriaSelect = document.getElementById('categoria');
    const generoSelect = document.getElementById('genero');
    const detailCategoriaSelect = document.getElementById('detail-categoria');
    const detailGeneroSelect = document.getElementById('detail-genero');

    const categoriaOptions = ['<option value="">Todas</option>', ...Object.entries(categorias).filter(([key]) => key > 1).map(([key, value]) => `<option value="${key}">${value}</option>`)].join('');
    const generoOptions = ['<option value="">Todos</option>', ...Object.entries(generos).filter(([key]) => key > 1).map(([key, value]) => `<option value="${key}">${value}</option>`)].join('');

    categoriaSelect.innerHTML = categoriaOptions;
    generoSelect.innerHTML = generoOptions;
    
    const detailCategoriaOptions = Object.entries(categorias).filter(([key]) => key > 1).map(([key, value]) => `<option value="${key}">${value}</option>`).join('');
    const detailGeneroOptions = Object.entries(generos).filter(([key]) => key > 1).map(([key, value]) => `<option value="${key}">${value}</option>`).join('');
    
    detailCategoriaSelect.innerHTML = `<option value="1"></option>${detailCategoriaOptions}`;
    detailGeneroSelect.innerHTML = `<option value="1"></option>${detailGeneroOptions}`;
}

function renderTable(data) {
    const tableBody = document.getElementById('results-body');
    tableBody.innerHTML = '';
    
    if (data.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="8" class="text-center p-4 text-gray-500">No se encontraron resultados.</td></tr>`;
    } else {
        data.forEach(book => {
            const autores = [book.autor1, book.autor2, book.autor3].filter(Boolean).join(', ');
            
            const imageName = book.portada || null;
            let miniaturaSrc = PLACEHOLDER_IMG;
            if (imageName) {
                miniaturaSrc = `images/miniaturas/${imageName}?v=${new Date().getTime()}`;
            }

            const categoriaNombre = book.categoria_nombre || '';
            const generoNombre = book.genero_nombre || '';
            const categoriaClasses = categoryColors[categoriaNombre.toUpperCase()] || 'bg-gray-100 text-gray-800';
            const generoClasses = genreColors[generoNombre.toUpperCase()] || 'bg-gray-100 text-gray-800';

            const row = `
                <tr class="hover:bg-gray-100">
                    <td class="p-3 text-sm text-gray-700 font-bold">${book.id}</td>
                    <td class="p-3 text-sm text-gray-700"><img src="${miniaturaSrc}" alt="Portada ${book.titulo}" class="w-12 h-auto rounded" onerror="this.onerror=null;this.src='${PLACEHOLDER_IMG}';"></td>
                    <td class="p-3 text-sm text-gray-900 font-medium">${book.titulo}</td>
                    <td class="p-3 text-sm text-gray-500">${autores}</td>
                    <td class="p-3 text-sm text-gray-500"><span class="px-2 py-1 text-xs font-semibold leading-tight rounded-full ${categoriaClasses}">${categoriaNombre}</span></td>
                    <td class="p-3 text-sm text-gray-500"><span class="px-2 py-1 text-xs font-semibold leading-tight rounded-full ${generoClasses}">${generoNombre}</span></td>
                    <td class="p-3 text-sm text-gray-500">${book.isbn || '-'}</td>
                    <td class="p-3 text-sm"><button onclick="showDetails(${book.id})" class="bg-indigo-500 text-white py-1 px-3 rounded-md hover:bg-indigo-600 font-semibold">Mostrar</button></td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    }
}


function clearForm() {
    document.getElementById('book-form').reset();
    document.getElementById('book-id').value = '';
    fetchedCoverUrl = null;
    document.getElementById('portada-preview-container').classList.add('hidden');
    document.getElementById('portada-preview-img').src = '';
}

function handleSearch() {
    currentSearchParams = {
        titulo: document.getElementById('titulo').value,
        autor: document.getElementById('autor').value,
        categoria: document.getElementById('categoria').value,
        genero: document.getElementById('genero').value,
        editorial: document.getElementById('editorial').value,
        isbn: document.getElementById('isbn').value,
    };
    loadBooks(1);
}

function showAll() {
    clearForm();
    currentSearchParams = {};
    currentSort = { key: 'id', order: 'desc' };
    document.querySelectorAll('.table-sortable th[data-sort]').forEach(h => {
         h.classList.remove('sort-asc', 'sort-desc');
         if(h.dataset.sort === 'id') h.classList.add('sort-desc');
    });
    loadBooks(1);
}

window.showDetails = function(bookId) {
    const book = libraryData.find(b => b.id == bookId);
    if (!book) return;
    
    const autores = [book.autor1, book.autor2, book.autor3].filter(Boolean).join(', ');
    const portadaSrc = book.portada ? `images/portadas/${book.portada}?v=${new Date().getTime()}` : 'https://placehold.co/400x600/EFEFEF/AAAAAA?text=Sin+Imagen';

    document.getElementById('detail-book-id').value = book.id;
    document.getElementById('detail-titulo').value = book.titulo;
    document.getElementById('detail-autor').value = autores;
    document.getElementById('detail-categoria').value = book.categoria;
    document.getElementById('detail-genero').value = book.genero;
    document.getElementById('detail-editorial').value = book.editorial;
    document.getElementById('detail-edicion').value = book.edicion;
    document.getElementById('detail-isbn').value = book.isbn;
    document.getElementById('detail-comentario').value = book.comentario;
    document.getElementById('detail-portada-img').src = portadaSrc;
    document.getElementById('detail-portada-file').value = '';
    document.getElementById('detail-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('detail-modal').classList.add('hidden');
}

async function handleSave(isUpdate) {
    const formIdPrefix = isUpdate ? 'detail-' : '';
    const fileInputId = isUpdate ? 'detail-portada-file' : 'portada';
    
    const titulo = document.getElementById(`${formIdPrefix}titulo`).value;
    const autoresStr = document.getElementById(`${formIdPrefix}autor`).value;
    const categoria = document.getElementById(`${formIdPrefix}categoria`).value;
    const genero = document.getElementById(`${formIdPrefix}genero`).value;
    
    if (!titulo || !autoresStr || !categoria || categoria === '1' || categoria === '' || !genero || genero === '1' || genero === '') {
        showToast('Título, Autor, Categoría y Género son campos obligatorios.', 'error');
        return;
    }

    const autores = autoresStr.split(',').map(a => a.trim());
    const formData = new FormData();
    
    formData.append('action', isUpdate ? 'update' : 'create');
    if (isUpdate) {
        formData.append('id', document.getElementById('detail-book-id').value);
    }

    formData.append('titulo', titulo);
    formData.append('autor1', autores[0] || '');
    formData.append('autor2', autores[1] || '');
    formData.append('autor3', autores[2] || '');
    formData.append('categoria', document.getElementById(`${formIdPrefix}categoria`).value);
    formData.append('genero', document.getElementById(`${formIdPrefix}genero`).value);
    formData.append('editorial', document.getElementById(`${formIdPrefix}editorial`).value);
    formData.append('edicion', document.getElementById(`${formIdPrefix}edicion`).value);
    formData.append('isbn', document.getElementById(`${formIdPrefix}isbn`).value);
    formData.append('comentario', document.getElementById(`${formIdPrefix}comentario`).value);

    const fileInput = document.getElementById(fileInputId);
    if (fileInput.files.length > 0) {
        formData.append('portada', fileInput.files[0]);
    } else if (fetchedCoverUrl && !isUpdate) {
        formData.append('cover_url', fetchedCoverUrl);
    }


    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            body: formData 
        });

        if (response.status === 401) {
             showToast('Su sesión ha expirado. Será redirigido al login.', 'error');
             setTimeout(() => window.location.href = 'login.php', 2000);
             return;
        }

        const result = await response.json();
         if (!response.ok) {
            throw new Error(result.details || 'Error desconocido del servidor');
        }

        if (result.success) {
            showToast(isUpdate ? 'Libro actualizado con éxito' : 'Libro añadido con éxito');
            loadBooks(isUpdate ? currentPage : 1);
        } else {
            throw new Error(result.error || 'Error desconocido del servidor');
        }
    } catch (error) {
        console.error("Error guardando en la BBDD:", error);
        showToast(`Error al guardar: ${error.message}`, 'error');
    }

    if (isUpdate) {
        closeModal();
    } else {
        clearForm();
    }
}

async function handleDelete() {
    const bookId = document.getElementById('detail-book-id').value;
    if (confirm('¿Estás seguro de que quieres eliminar este libro? Se borrarán también sus imágenes del servidor.')) {
         const formData = new FormData();
         formData.append('action', 'delete');
         formData.append('id', bookId);

        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                body: formData
            });

            if (response.status === 401) {
                showToast('Su sesión ha expirado. Será redirigido al login.', 'error');
                setTimeout(() => window.location.href = 'login.php', 2000);
                return;
            }
            
            const result = await response.json();
            if (!response.ok) {
                throw new Error(result.details || 'Error desconocido del servidor');
            }

            if (result.success) {
                closeModal();
                showToast('Libro eliminado correctamente.', 'success');
                loadBooks(currentPage);
            } else {
                throw new Error(result.error || 'Error desconocido del servidor');
            }
        } catch (error) {
             console.error("Error eliminando de la BBDD:", error);
            showToast(`Error al eliminar: ${error.message}`, 'error');
        }
    }
}

