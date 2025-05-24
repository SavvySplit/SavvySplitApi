# ocr.py
import sys
import easyocr
import json
import os
import numpy as np
from pdf2image import convert_from_path
import warnings

# Suppress all warnings and logs
warnings.filterwarnings("ignore")
os.environ["TF_CPP_MIN_LOG_LEVEL"] = "3"
os.environ["TORCH_CPP_LOG_LEVEL"] = "ERROR"
os.environ["PYTORCH_JIT_LOG_LEVEL"] = "ERROR"
os.environ["KMP_WARNINGS"] = "0"
sys.stderr = open(os.devnull, 'w')  # Suppress stderr logs

file_path = sys.argv[1]
reader = easyocr.Reader(['en'], gpu=False)  # Use CPU for stability

extracted_text = []

try:
    if file_path.lower().endswith('.pdf'):
        # Linux poppler path (adjust if needed)
        images = convert_from_path(file_path, poppler_path='/usr/bin', dpi=300)
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
