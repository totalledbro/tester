@extends('layouts.lay')

@section('content')
<div class="pdf-container">
    <div class="pdf-controls">
        <button id="prev" class="control-button"><ion-icon name="arrow-back-circle"></ion-icon></button>
        <button id="page-info" class="page-info-button"><span id="page-count"></span></button>
        <button id="next" class="control-button"><ion-icon name="arrow-forward-circle"></ion-icon></button>
    </div>
    <canvas id="pdf-viewer"></canvas>
    <div class="pdf-controls">
        <button id="prev" class="control-button"><ion-icon name="arrow-back-circle"></ion-icon></button>
        <button id="page-info" class="page-info-button"><span id="page-count"></span></button>
        <button id="next" class="control-button"><ion-icon name="arrow-forward-circle"></ion-icon></button>
    </div>
</div>

<!-- Modal -->
<div id="page-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <label for="page-input-modal"> Buka halaman:</label>
        <input type="text" id="page-input-modal" min="1" class="page-input" oninput="this.value = this.value.replace(/[^0-9]/g, ''); " autocomplete="off">
        <button id="go-to-page" class="control-button">Go</button>
    </div>
</div>
@endsection
<script>
// JavaScript for enforcing numeric input
document.getElementById('page-input-modal').addEventListener('input', function() {
    // Remove non-numeric characters from input
    this.value = this.value.replace(/\D/g, '');
});
</script>
<script type="module">
import * as pdfjsLib from 'https://cdn.jsdelivr.net/npm/pdfjs-dist@4.2.67/build/pdf.min.mjs';
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdn.jsdelivr.net/npm/pdfjs-dist@4.2.67/build/pdf.worker.min.mjs';

document.addEventListener('DOMContentLoaded', function() {
    const url = 'http://127.0.0.1:8000/get-book-pdf/{{ $loan->id }}';

    let pdfDoc = null,
        pageNum = 1,
        pageIsRendering = false,
        pageNumIsPending = null;

    const scale = 2,
          canvas = document.getElementById('pdf-viewer'),
          ctx = canvas.getContext('2d');

    const renderPage = num => {
        pageIsRendering = true;

        pdfDoc.getPage(num).then(page => {
            const viewport = page.getViewport({ scale });
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            const renderCtx = {
                canvasContext: ctx,
                viewport
            };

            page.render(renderCtx).promise.then(() => {
                pageIsRendering = false;
                if (pageNumIsPending !== null) {
                    renderPage(pageNumIsPending);
                    pageNumIsPending = null;
                }
            });

            document.querySelectorAll('.page-info-button span').forEach(span => span.textContent = `${num} dari ${pdfDoc.numPages}`);
        });

        document.querySelectorAll('#prev').forEach(button => button.disabled = num <= 1);
        document.querySelectorAll('#next').forEach(button => button.disabled = num >= pdfDoc.numPages);
    };

    const queueRenderPage = num => {
        if (pageIsRendering) {
            pageNumIsPending = num;
        } else {
            renderPage(num);
        }
    };

    document.querySelectorAll('#prev').forEach(button => button.addEventListener('click', () => {
        if (pageNum <= 1) return;
        pageNum--;
        queueRenderPage(pageNum);
    }));

    document.querySelectorAll('#next').forEach(button => button.addEventListener('click', () => {
        if (pageNum >= pdfDoc.numPages) return;
        pageNum++;
        queueRenderPage(pageNum);
    }));

    pdfjsLib.getDocument(url).promise.then(pdfDoc_ => {
        pdfDoc = pdfDoc_;
        renderPage(pageNum);
    }).catch(err => {
        const div = document.createElement('div');
        div.className = 'error';
        div.appendChild(document.createTextNode(err.message));
        canvas.parentNode.insertBefore(div, canvas);
        document.querySelectorAll('.pdf-controls').forEach(control => control.style.display = 'none');
    });

    canvas.addEventListener('click', (e) => {
        const rect = canvas.getBoundingClientRect();
        const x = e.clientX - rect.left;
        if (x < rect.width / 2) {
            if (pageNum > 1) {
                pageNum--;
                queueRenderPage(pageNum);
            }
        } else {
            if (pageNum < pdfDoc.numPages) {
                pageNum++;
                queueRenderPage(pageNum);
            }
        }
    });

    // Modal functionality
    const modal = document.getElementById('page-modal');
    const modalClose = document.querySelector('.modal .close');
    const pageInputModal = document.getElementById('page-input-modal');
    const goToPageBtn = document.getElementById('go-to-page');

    document.querySelectorAll('#page-info').forEach(button => button.addEventListener('click', () => {
        modal.style.display = 'block';
        pageInputModal.value = pageNum;
        pageInputModal.max = pdfDoc.numPages;
    }));

    modalClose.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    goToPageBtn.addEventListener('click', () => {
        const num = parseInt(pageInputModal.value);
        if (num > 0 && num <= pdfDoc.numPages) {
            pageNum = num;
            queueRenderPage(pageNum);
            modal.style.display = 'none';
        }
    });

    window.addEventListener('click', (e) => {
        if (e.target == modal) {
            modal.style.display = 'none';
        }
    });
});
</script>

<style>
.pdf-container {
    text-align: center;
    width: 100%;
    background-color: rgba(100, 100, 100, 0.5);
    padding: 0;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    box-sizing: border-box;
    user-select: none;
    -webkit-user-select: none; /* Chrome, Safari */
    -moz-user-select: none;    /* Firefox */
    -ms-user-select: none; 
}

.pdf-controls {
    margin-bottom: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
}

.pdf-controls .control-button,
.pdf-controls .page-info-button {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 32px;
    color: white;
    margin: 0 5px;
    transition: color 0.3s ease;
}

.pdf-controls .control-button:hover,
.pdf-controls .page-info-button:hover {
    color: #0056b3;
}

canvas {
    max-width: 100%;
    height: auto;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.error {
    color: red;
    margin-top: 20px;
}

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 300px;
    border-radius: 10px;
    text-align: center;
}

.modal .close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.modal .close:hover,
.modal .close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.modal .control-button {
    background: #007bff;
    border: none;
    cursor: pointer;
    color: white;
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 5px;
    transition: background 0.3s ease;
}

.modal .control-button:hover {
    background: #0056b3;
}

/* Responsive Styles */
@media (max-width: 600px) {
    .pdf-controls .control-button,
    .pdf-controls .page-info-button {
        font-size: 28px;
    }

    canvas {
        width: 100%;
    }

    .modal-content {
        width: 90%;
        margin: 20% auto;
    }
}
</style>
