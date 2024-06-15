import sys
from PIL import Image, ImageDraw, ImageFont
import os

def main(image_path, processed_image_path):
    try:
        # Открываем входное изображение
        with Image.open(image_path) as img:
            # Создаем объект ImageDraw
            draw = ImageDraw.Draw(img)

            # Рисуем прямоугольник (коробка)
            draw.rectangle([50, 50, 200, 200], outline="red", width=3)

            # Добавляем текст
            font = ImageFont.truetype("arial.ttf", size=30)  # Выбираем шрифт и размер
            draw.text((50, 210), "Hello, Pillow!", font=font, fill="blue")

            # Сохраняем измененное изображение
            img.save(processed_image_path)
            print(f"Processed image saved at: {processed_image_path}")
    except Exception as e:
        print(f"An error occurred: {e}")

if __name__ == "__main__":
    if len(sys.argv) > 2:
        main(sys.argv[1], sys.argv[2])
    else:
        print("Usage: python your_script.py <input_image_path> <processed_image_path>")
