#!/usr/bin/env python
# predict.py

import os
import sys
import io
import logging

# ---------------------------------------------------------
# 1) MATIKAN SELURUH LOG TENSORFLOW SEBELUM IMPORT TENSORFLOW
# ---------------------------------------------------------
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'   # 0 = semua log, 3 = error saja
logging.getLogger('tensorflow').setLevel(logging.ERROR)

# ---------------------------------------------------------
# 2) IMPORT MODUL‐MODUL LAIN
# ---------------------------------------------------------
import numpy as np
import tensorflow as tf
from tensorflow.keras.models import load_model
from tensorflow.keras.preprocessing import image

# ---------------------------------------------------------
# 3) PASTIKAN stdout di‐wrap UTF-8 agar tidak terjadi error charmap
# ---------------------------------------------------------
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

def main():
    # 4) Periksa argumen (path gambar) harus diberikan
    if len(sys.argv) < 2:
        print("Usage: python predict.py <path_to_image>")
        sys.exit(1)

    img_path = sys.argv[1]
    if not os.path.exists(img_path):
        print(f"Error: File gambar tidak ditemukan di path: {img_path}")
        sys.exit(1)

    # 5) Tentukan direktori root (tempat predict.py berada)
    root_dir = os.path.dirname(os.path.abspath(__file__))
    # model harus diletakkan di: sitanilaravel/model/model_cnn_tanaman.keras
    model_dir = os.path.join(root_dir, "model")
    model_filename = "model_cnn_tanaman.keras"
    model_path = os.path.join(model_dir, model_filename)

    if not os.path.exists(model_path):
        print(f"Model file tidak ditemukan: {model_path}")
        sys.exit(1)

    # 6) Load model Keras
    try:
        model = load_model(model_path)
    except Exception as e:
        print("Error saat load model: " + str(e))
        sys.exit(1)

    # 7) Daftar label kelas (harus persis sama urutannya seperti saat training)
    labels = ['Healthy', 'Leaf Curl', 'Leaf Spot', 'White Fly', 'Yellowish']

    # 8) Preprocess gambar
    try:
        img = image.load_img(img_path, target_size=(224, 224))
        img_array = image.img_to_array(img).astype('float32') / 255.0
        img_array = np.expand_dims(img_array, axis=0)  # bentuk: (1,224,224,3)
    except Exception as e:
        print("Error preprocessing gambar: " + str(e))
        sys.exit(1)

    # 9) Prediksi (tanpa progress bar)
    try:
        preds = model.predict(img_array, verbose=0)
        predicted_index = np.argmax(preds, axis=1)[0]
    except Exception as e:
        print("Error saat prediksi: " + str(e))
        sys.exit(1)

    # 10) Ambil label sesuai indeks
    if 0 <= predicted_index < len(labels):
        predicted_label = labels[predicted_index]
    else:
        predicted_label = "Unknown"

    # 11) Cetak hanya label (tanpa log TensorFlow, tanpa timestamps)
    print(predicted_label)

if __name__ == "__main__":
    main()
