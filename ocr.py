# ocr.py
import sys
import easyocr
import json
import os
import numpy as np
from pdf2image import convert_from_path
import warnings


# Suppress all warnings (like PyTorch's pin_memory one)
warnings.filterwarnings("ignore")
sys.stderr = open(os.devnull, 'w')


file_path = sys.argv[1]

reader = easyocr.Reader(['en'])
extracted_text = []

try:
    if file_path.lower().endswith('.pdf'):
        # Convert PDF pages to images
        #images = convert_from_path(file_path, poppler_path="/opt/homebrew/bin", dpi=400)  # Set poppler_path if needed
        images = convert_from_path(file_path, poppler_path='/usr/bin', dpi=400)  # Set poppler_path if needed

        for image in images:
            text = reader.readtext(np.array(image), detail=0)
            extracted_text.extend(text)
    else:
        text = reader.readtext(file_path, detail=0)
        extracted_text.extend(text)

    print(json.dumps({"text": "\n".join(extracted_text)}))
except Exception as e:
    print(json.dumps({"error": str(e)}))
    sys.exit(1)
