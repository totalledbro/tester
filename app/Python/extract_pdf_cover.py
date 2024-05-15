import os
import fitz  # PyMuPDF
from PIL import Image
import io
import sys

def save_pdf_cover(pdf_path, output_dir):
    try:
        os.makedirs(output_dir, exist_ok=True)
        
        # Print current working directory
        print("Current working directory:", os.getcwd())
        
        pdf_path = os.path.abspath(pdf_path)
        output_dir = os.path.abspath(output_dir)
        
        # Print absolute paths
        print("Absolute PDF path:", pdf_path)
        print("Absolute output directory:", output_dir)

        if not os.path.isfile(pdf_path):
            print(f"Error: File does not exist - {pdf_path}")
            sys.exit(1)

        pdf_document = fitz.open(pdf_path)
        first_page = pdf_document.load_page(0)
        pix = first_page.get_pixmap()
        img_data = pix.tobytes("png")
        image = Image.open(io.BytesIO(img_data))

        pdf_name = os.path.basename(pdf_path)
        image_name = os.path.splitext(pdf_name)[0] + '.png'
        output_path = os.path.join(output_dir, image_name)

        image.save(output_path, "PNG")
        print(f"Cover image saved to: {output_path}")
        
        return output_path
    except Exception as e:
        print(f"An error occurred: {e}")
        sys.exit(1)

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: python extract_pdf_cover.py <pdf_path> <output_dir>")
        sys.exit(1)
    
    pdf_path = sys.argv[1]
    output_dir = sys.argv[2]
    
    save_pdf_cover(pdf_path, output_dir)
