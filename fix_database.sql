-- Database'dagi mahsulot rasmlarini NULL qilish
-- Bu skriptni ishga tushiring agar database allaqachon import qilingan bo'lsa

USE onlineshop;

UPDATE products SET image = NULL;
