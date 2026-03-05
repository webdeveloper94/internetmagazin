USE onlineshop;

-- 1. Parfyumeriya (id=1)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(1, 'Chanel No 5 atir', 'Klassik ayollar parfyumi, 100ml', 850000, 'prod_69a9b46d9dac1.jpg', 0),
(1, 'Dior Sauvage erkaklar atiri', 'Mashhur erkaklar parfyumi, 100ml', 920000, 'prod_69a9b46d9dac1.jpg', 0),
(1, 'Versace Bright Crystal', 'Ayollar uchun yengil parfyum', 650000, 'prod_69a9b46d9dac1.jpg', 0),
(1, 'Hugo Boss Bottled', 'Erkaklar uchun klassik atir', 480000, 'prod_69a9b46d9dac1.jpg', 0),
(1, 'Armani Code', 'Erkaklar kechki parfyumi', 720000, 'prod_69a9b46d9dac1.jpg', 0),
(1, 'Lancome La Vie Est Belle', 'Ayollar uchun shirin parfyum', 790000, 'prod_69a9b46d9dac1.jpg', 0),
(1, 'Calvin Klein Eternity', 'Uniseks klassik atir', 430000, 'prod_69a9b46d9dac1.jpg', 0),
(1, 'Paco Rabanne 1 Million', 'Erkaklar uchun zolotoy parfyum', 680000, 'prod_69a9b46d9dac1.jpg', 0),
(1, 'Dolce & Gabbana Light Blue', 'Yozgi yengil parfyum', 560000, 'prod_69a9b46d9dac1.jpg', 0),
(1, 'Tom Ford Black Orchid', 'Premium ayollar parfyumi', 1200000, 'prod_69a9b46d9dac1.jpg', 0);

-- 2. Elektronika (id=2)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(2, 'Samsung Galaxy S24 Ultra', '256GB, Titanium Gray, AMOLED ekran', 15900000, 'prod_69a9b46d9dac1.jpg', 0),
(2, 'iPhone 15 Pro Max', '256GB, Natural Titanium', 17500000, 'prod_69a9b46d9dac1.jpg', 0),
(2, 'MacBook Air M3', '13 dyuym, 8GB RAM, 256GB SSD', 14200000, 'prod_69a9b46d9dac1.jpg', 0),
(2, 'Samsung Galaxy Tab S9', '128GB, Wi-Fi, 11 dyuym planshet', 6800000, 'prod_69a9b46d9dac1.jpg', 0),
(2, 'AirPods Pro 2', 'Shovqinni kamaytirish funksiyali quloqchin', 3200000, 'prod_69a9b46d9dac1.jpg', 0),
(2, 'JBL Charge 5 kolonka', 'Suv o''tkazmaydigan bluetooth kolonka', 1450000, 'prod_69a9b46d9dac1.jpg', 0),
(2, 'Xiaomi Redmi Note 13 Pro', '256GB, 8GB RAM, 200MP kamera', 3900000, 'prod_69a9b46d9dac1.jpg', 0),
(2, 'Sony WH-1000XM5', 'Premium simsiz quloqchin', 4500000, 'prod_69a9b46d9dac1.jpg', 0),
(2, 'Lenovo IdeaPad 3', '15.6 dyuym, i5, 8GB RAM, 512GB SSD', 7200000, 'prod_69a9b46d9dac1.jpg', 0),
(2, 'Samsung 50" QLED TV', '4K Smart TV, 50 dyuym', 8900000, 'prod_69a9b46d9dac1.jpg', 0);

-- 3. Maishiy texnika (id=3)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(3, 'Samsung kir yuvish mashinasi 8kg', 'Avtomat, 1200 aylanish, invertor motor', 4500000, 'prod_69a9b46d9dac1.jpg', 0),
(3, 'LG muzlatgich ikki eshikli', 'No Frost, 453L hajm, kumush rang', 6200000, 'prod_69a9b46d9dac1.jpg', 0),
(3, 'Dyson V15 changyutgich', 'Simsiz vertikal changyutgich', 7800000, 'prod_69a9b46d9dac1.jpg', 0),
(3, 'Bosch idish yuvish mashinasi', '14 komplekt, 6 dastur', 5400000, 'prod_69a9b46d9dac1.jpg', 0),
(3, 'Artel konditsioner 12BTU', 'Invertor, isitish va sovutish', 3800000, 'prod_69a9b46d9dac1.jpg', 0),
(3, 'Philips dazmol', 'Bug''li dazmol, 2800W', 450000, 'prod_69a9b46d9dac1.jpg', 0),
(3, 'Xiaomi Robot changyutgich', 'Aqlli robot changyutgich, Wi-Fi', 3200000, 'prod_69a9b46d9dac1.jpg', 0),
(3, 'Samsung mikroto''lqinli pech', '23L, 800W, oq rang', 890000, 'prod_69a9b46d9dac1.jpg', 0),
(3, 'Electrolux quritgich', 'Kir quritish mashinasi, 8kg', 4100000, 'prod_69a9b46d9dac1.jpg', 0),
(3, 'Ariston suv isitgich 80L', 'Elektr suv isitgich, vertikal', 1350000, 'prod_69a9b46d9dac1.jpg', 0);

-- 4. Kiyim-kechak (id=4)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(4, 'Erkaklar klassik ko''ylagi', 'Paxta, oq rang, slim fit', 189000, 'prod_69a9b46d9dac1.jpg', 1),
(4, 'Ayollar yozgi ko''ylagi', 'Yengil mato, gul naqshli', 145000, 'prod_69a9b46d9dac1.jpg', 1),
(4, 'Erkaklar jinsi shim', 'Klassik to''q ko''k jinsi', 250000, 'prod_69a9b46d9dac1.jpg', 1),
(4, 'Ayollar sport kostyumi', 'Paxta aralash, qora rang', 320000, 'prod_69a9b46d9dac1.jpg', 1),
(4, 'Erkaklar qishki kurtka', 'Issiq to''ldirilgan, suv o''tkazmaydigan', 580000, 'prod_69a9b46d9dac1.jpg', 1),
(4, 'Ayollar palto', 'Klassik ayollar paltosi, jigarrang', 890000, 'prod_69a9b46d9dac1.jpg', 1),
(4, 'Bolalar futbolkasi', 'Paxta, turli ranglar', 65000, 'prod_69a9b46d9dac1.jpg', 1),
(4, 'Erkaklar polo futbolka', 'Lacoste uslubida, paxta', 175000, 'prod_69a9b46d9dac1.jpg', 1),
(4, 'Ayollar ro''mol', 'Ipak ro''mol, turli naqshlar', 120000, 'prod_69a9b46d9dac1.jpg', 0),
(4, 'Erkaklar kostyum to''plami', 'Klassik kostyum: ko''ylak + shim', 1200000, 'prod_69a9b46d9dac1.jpg', 1);

-- Kiyim o'lchamlari
INSERT INTO product_sizes (product_id, size_name, price) VALUES
-- Erkaklar klassik ko'ylagi
((SELECT id FROM products WHERE name='Erkaklar klassik ko''ylagi' LIMIT 1), 'S', 189000),
((SELECT id FROM products WHERE name='Erkaklar klassik ko''ylagi' LIMIT 1), 'M', 189000),
((SELECT id FROM products WHERE name='Erkaklar klassik ko''ylagi' LIMIT 1), 'L', 199000),
((SELECT id FROM products WHERE name='Erkaklar klassik ko''ylagi' LIMIT 1), 'XL', 209000),
-- Ayollar yozgi ko'ylagi
((SELECT id FROM products WHERE name='Ayollar yozgi ko''ylagi' LIMIT 1), 'S', 145000),
((SELECT id FROM products WHERE name='Ayollar yozgi ko''ylagi' LIMIT 1), 'M', 145000),
((SELECT id FROM products WHERE name='Ayollar yozgi ko''ylagi' LIMIT 1), 'L', 155000),
-- Erkaklar jinsi shim
((SELECT id FROM products WHERE name='Erkaklar jinsi shim' LIMIT 1), '30', 250000),
((SELECT id FROM products WHERE name='Erkaklar jinsi shim' LIMIT 1), '32', 250000),
((SELECT id FROM products WHERE name='Erkaklar jinsi shim' LIMIT 1), '34', 260000),
((SELECT id FROM products WHERE name='Erkaklar jinsi shim' LIMIT 1), '36', 270000),
-- Ayollar sport kostyumi
((SELECT id FROM products WHERE name='Ayollar sport kostyumi' LIMIT 1), 'S', 320000),
((SELECT id FROM products WHERE name='Ayollar sport kostyumi' LIMIT 1), 'M', 320000),
((SELECT id FROM products WHERE name='Ayollar sport kostyumi' LIMIT 1), 'L', 340000),
-- Erkaklar qishki kurtka
((SELECT id FROM products WHERE name='Erkaklar qishki kurtka' LIMIT 1), 'M', 580000),
((SELECT id FROM products WHERE name='Erkaklar qishki kurtka' LIMIT 1), 'L', 580000),
((SELECT id FROM products WHERE name='Erkaklar qishki kurtka' LIMIT 1), 'XL', 600000),
((SELECT id FROM products WHERE name='Erkaklar qishki kurtka' LIMIT 1), 'XXL', 620000),
-- Ayollar palto
((SELECT id FROM products WHERE name='Ayollar palto' LIMIT 1), 'S', 890000),
((SELECT id FROM products WHERE name='Ayollar palto' LIMIT 1), 'M', 890000),
((SELECT id FROM products WHERE name='Ayollar palto' LIMIT 1), 'L', 920000),
-- Bolalar futbolkasi
((SELECT id FROM products WHERE name='Bolalar futbolkasi' LIMIT 1), '3-4 yosh', 65000),
((SELECT id FROM products WHERE name='Bolalar futbolkasi' LIMIT 1), '5-6 yosh', 70000),
((SELECT id FROM products WHERE name='Bolalar futbolkasi' LIMIT 1), '7-8 yosh', 75000),
-- Erkaklar polo futbolka
((SELECT id FROM products WHERE name='Erkaklar polo futbolka' LIMIT 1), 'M', 175000),
((SELECT id FROM products WHERE name='Erkaklar polo futbolka' LIMIT 1), 'L', 175000),
((SELECT id FROM products WHERE name='Erkaklar polo futbolka' LIMIT 1), 'XL', 185000),
-- Erkaklar kostyum to'plami
((SELECT id FROM products WHERE name='Erkaklar kostyum to''plami' LIMIT 1), '48', 1200000),
((SELECT id FROM products WHERE name='Erkaklar kostyum to''plami' LIMIT 1), '50', 1200000),
((SELECT id FROM products WHERE name='Erkaklar kostyum to''plami' LIMIT 1), '52', 1250000),
((SELECT id FROM products WHERE name='Erkaklar kostyum to''plami' LIMIT 1), '54', 1300000);

-- 5. Poyabzallar (id=5)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(5, 'Nike Air Max 90', 'Klassik sport poyabzal, oq rang', 1250000, 'prod_69a9b46d9dac1.jpg', 1),
(5, 'Adidas Ultraboost', 'Yugurish uchun poyabzal', 1450000, 'prod_69a9b46d9dac1.jpg', 1),
(5, 'Erkaklar klassik tufli', 'Charm, qora rang', 650000, 'prod_69a9b46d9dac1.jpg', 1),
(5, 'Ayollar balandposhnali tufli', 'Elegant ayollar tuflisi', 480000, 'prod_69a9b46d9dac1.jpg', 1),
(5, 'Bolalar krossovkasi', 'Yengil va qulay bolalar poyabzali', 280000, 'prod_69a9b46d9dac1.jpg', 1),
(5, 'Erkaklar qishki etik', 'Issiq, charm qishki etik', 890000, 'prod_69a9b46d9dac1.jpg', 1),
(5, 'Ayollar baletka', 'Kundalik qulay baletka', 195000, 'prod_69a9b46d9dac1.jpg', 1),
(5, 'Puma sport shippak', 'Yozgi sport shippak', 320000, 'prod_69a9b46d9dac1.jpg', 1),
(5, 'New Balance 574', 'Klassik sport poyabzal', 980000, 'prod_69a9b46d9dac1.jpg', 1),
(5, 'Skechers yurish poyabzali', 'Memory Foam, juda qulay', 750000, 'prod_69a9b46d9dac1.jpg', 1);

-- Poyabzal o'lchamlari
INSERT INTO product_sizes (product_id, size_name, price) VALUES
((SELECT id FROM products WHERE name='Nike Air Max 90' LIMIT 1), '40', 1250000),
((SELECT id FROM products WHERE name='Nike Air Max 90' LIMIT 1), '41', 1250000),
((SELECT id FROM products WHERE name='Nike Air Max 90' LIMIT 1), '42', 1250000),
((SELECT id FROM products WHERE name='Nike Air Max 90' LIMIT 1), '43', 1300000),
((SELECT id FROM products WHERE name='Adidas Ultraboost' LIMIT 1), '41', 1450000),
((SELECT id FROM products WHERE name='Adidas Ultraboost' LIMIT 1), '42', 1450000),
((SELECT id FROM products WHERE name='Adidas Ultraboost' LIMIT 1), '43', 1450000),
((SELECT id FROM products WHERE name='Erkaklar klassik tufli' LIMIT 1), '40', 650000),
((SELECT id FROM products WHERE name='Erkaklar klassik tufli' LIMIT 1), '41', 650000),
((SELECT id FROM products WHERE name='Erkaklar klassik tufli' LIMIT 1), '42', 650000),
((SELECT id FROM products WHERE name='Ayollar balandposhnali tufli' LIMIT 1), '36', 480000),
((SELECT id FROM products WHERE name='Ayollar balandposhnali tufli' LIMIT 1), '37', 480000),
((SELECT id FROM products WHERE name='Ayollar balandposhnali tufli' LIMIT 1), '38', 480000),
((SELECT id FROM products WHERE name='Bolalar krossovkasi' LIMIT 1), '28', 280000),
((SELECT id FROM products WHERE name='Bolalar krossovkasi' LIMIT 1), '30', 290000),
((SELECT id FROM products WHERE name='Bolalar krossovkasi' LIMIT 1), '32', 300000),
((SELECT id FROM products WHERE name='Erkaklar qishki etik' LIMIT 1), '41', 890000),
((SELECT id FROM products WHERE name='Erkaklar qishki etik' LIMIT 1), '42', 890000),
((SELECT id FROM products WHERE name='Erkaklar qishki etik' LIMIT 1), '43', 920000),
((SELECT id FROM products WHERE name='Ayollar baletka' LIMIT 1), '36', 195000),
((SELECT id FROM products WHERE name='Ayollar baletka' LIMIT 1), '37', 195000),
((SELECT id FROM products WHERE name='Ayollar baletka' LIMIT 1), '38', 195000),
((SELECT id FROM products WHERE name='Puma sport shippak' LIMIT 1), '40', 320000),
((SELECT id FROM products WHERE name='Puma sport shippak' LIMIT 1), '41', 320000),
((SELECT id FROM products WHERE name='Puma sport shippak' LIMIT 1), '42', 320000),
((SELECT id FROM products WHERE name='New Balance 574' LIMIT 1), '41', 980000),
((SELECT id FROM products WHERE name='New Balance 574' LIMIT 1), '42', 980000),
((SELECT id FROM products WHERE name='New Balance 574' LIMIT 1), '43', 1000000),
((SELECT id FROM products WHERE name='Skechers yurish poyabzali' LIMIT 1), '40', 750000),
((SELECT id FROM products WHERE name='Skechers yurish poyabzali' LIMIT 1), '41', 750000),
((SELECT id FROM products WHERE name='Skechers yurish poyabzali' LIMIT 1), '42', 780000);

-- 6. Oziq-ovqat (id=6)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(6, 'Baraka non yog''i 1L', 'Tabiiy o''simlik yog''i', 28000, 'prod_69a9b46d9dac1.jpg', 0),
(6, 'Coca-Cola 1.5L', 'Gazlangan ichimlik', 12000, 'prod_69a9b46d9dac1.jpg', 0),
(6, 'Nestle shokolad 100g', 'Sut shokoladi', 15000, 'prod_69a9b46d9dac1.jpg', 0),
(6, 'Makfa makaron 400g', 'Yuqori sifatli makaron', 9500, 'prod_69a9b46d9dac1.jpg', 0),
(6, 'Lipton choy 100 paket', 'Qora choy, paketli', 35000, 'prod_69a9b46d9dac1.jpg', 0),
(6, 'Nescafe Gold 190g', 'Eritma kofe, shisha idish', 85000, 'prod_69a9b46d9dac1.jpg', 0),
(6, 'Guruch Lazat 5kg', 'Devzira guruchi', 75000, 'prod_69a9b46d9dac1.jpg', 0),
(6, 'Shakar 1kg', 'Oq shakar', 14000, 'prod_69a9b46d9dac1.jpg', 0),
(6, 'Toshkent noni', 'An''anaviy o''zbek noni', 6000, 'prod_69a9b46d9dac1.jpg', 0),
(6, 'Sut Lactel 1L', 'Pasterizatsiya qilingan sut, 3.2%', 16000, 'prod_69a9b46d9dac1.jpg', 0);

-- 7. Go'zallik va salomatlik (id=7)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(7, 'Nivea yuz kremi 50ml', 'Namlovchi yuz kremi', 65000, 'prod_69a9b46d9dac1.jpg', 0),
(7, 'Head & Shoulders shampun 400ml', 'Kepakka qarshi shampun', 42000, 'prod_69a9b46d9dac1.jpg', 0),
(7, 'Oral-B elektr tish cho''tkasi', 'Qayta zaryadlanuvchi tish cho''tkasi', 350000, 'prod_69a9b46d9dac1.jpg', 0),
(7, 'Maybelline tush 10ml', 'Kiprik uchun qora tush', 89000, 'prod_69a9b46d9dac1.jpg', 0),
(7, 'Dove dush geli 500ml', 'Namlovchi dush geli', 38000, 'prod_69a9b46d9dac1.jpg', 0),
(7, 'Gillette skrabka 4 dona', 'Erkaklar uchun almashtiruvchan pichoqlar', 95000, 'prod_69a9b46d9dac1.jpg', 0),
(7, 'L''Oreal soch bo''yog''i', 'Professional soch bo''yog''i', 55000, 'prod_69a9b46d9dac1.jpg', 0),
(7, 'Pantene soch konditsioneri 400ml', 'Qayta tiklash uchun', 36000, 'prod_69a9b46d9dac1.jpg', 0),
(7, 'Rexona dezodorant 150ml', 'Terga qarshi dezodorant', 28000, 'prod_69a9b46d9dac1.jpg', 0),
(7, 'Johnson''s bolalar kremi 200ml', 'Bolalar uchun yumshoq krem', 32000, 'prod_69a9b46d9dac1.jpg', 0);

-- 8. Uy va bog' (id=8)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(8, 'IKEA kitob javoni', 'Yog''och kitob javoni, 5 qavat', 1200000, 'prod_69a9b46d9dac1.jpg', 0),
(8, 'LED yoritgich 12W', 'Energiya tejovchi lampochka', 18000, 'prod_69a9b46d9dac1.jpg', 0),
(8, 'Parda to''plami 2 dona', 'Yotoqxona uchun qalin parda', 350000, 'prod_69a9b46d9dac1.jpg', 0),
(8, 'Gilam 2x3m', 'Yumshoq gilam, zamonaviy dizayn', 890000, 'prod_69a9b46d9dac1.jpg', 0),
(8, 'Bog'' shlang 20m', 'Suv berish uchun shlang', 120000, 'prod_69a9b46d9dac1.jpg', 0),
(8, 'Ko''chat qaychi', 'Professional bog'' qaychi', 85000, 'prod_69a9b46d9dac1.jpg', 0),
(8, 'Matras 160x200', 'Ortopedik matras, o''rtacha qattiqlik', 2800000, 'prod_69a9b46d9dac1.jpg', 0),
(8, 'Yostiq 50x70 2 dona', 'Antiallergik yostiq', 180000, 'prod_69a9b46d9dac1.jpg', 0),
(8, 'Dekorativ gul guldon', 'Keramik guldon, balandligi 30sm', 95000, 'prod_69a9b46d9dac1.jpg', 0),
(8, 'Stol lampa', 'Zamonaviy LED stol lampasi', 220000, 'prod_69a9b46d9dac1.jpg', 0);

-- 9. Bolalar uchun (id=9)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(9, 'LEGO Constructor 500 det', 'Klassik LEGO konstruktori', 450000, 'prod_69a9b46d9dac1.jpg', 0),
(9, 'Bolalar velosipedi 16"', '4-6 yosh bolalar uchun', 850000, 'prod_69a9b46d9dac1.jpg', 0),
(9, 'Yumshoq ayiqcha o''yinchoq', 'Katta yumshoq o''yinchoq, 60sm', 180000, 'prod_69a9b46d9dac1.jpg', 0),
(9, 'Bolalar rasm to''plami', '24 rangli qalamlar va albom', 65000, 'prod_69a9b46d9dac1.jpg', 0),
(9, 'Bolalar qishki kombinezon', 'Issiq qishki kiyim', 420000, 'prod_69a9b46d9dac1.jpg', 1),
(9, 'Baby Monitor kamera', 'Bolani kuzatish kamerasi, Wi-Fi', 580000, 'prod_69a9b46d9dac1.jpg', 0),
(9, 'Bolalar avtokreslosi', 'Xavfsiz avtomobil kreслosi, 9-36kg', 1200000, 'prod_69a9b46d9dac1.jpg', 0),
(9, 'Bolalar uchun puzzle 1000', 'Rivojlantiruvchi puzzle o''yini', 85000, 'prod_69a9b46d9dac1.jpg', 0),
(9, 'Bolalar kolyaskasi', 'Universal bolalar aravasi', 2500000, 'prod_69a9b46d9dac1.jpg', 0),
(9, 'Bolalar cho''milish to''plami', 'Vanna, sochiq va shampun', 250000, 'prod_69a9b46d9dac1.jpg', 0);

INSERT INTO product_sizes (product_id, size_name, price) VALUES
((SELECT id FROM products WHERE name='Bolalar qishki kombinezon' LIMIT 1), '1-2 yosh', 420000),
((SELECT id FROM products WHERE name='Bolalar qishki kombinezon' LIMIT 1), '2-3 yosh', 450000),
((SELECT id FROM products WHERE name='Bolalar qishki kombinezon' LIMIT 1), '3-4 yosh', 480000);

-- 10. Sport va dam olish (id=10)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(10, 'Gantel to''plami 20kg', 'Sozlanuvchi gantel, 2x10kg', 650000, 'prod_69a9b46d9dac1.jpg', 0),
(10, 'Yoga mat', 'Sirpanmaydigan yoga gilami, 6mm', 120000, 'prod_69a9b46d9dac1.jpg', 0),
(10, 'Velosiped Trinx M136', 'Tog'' velosipedi, 26 dyuym', 3200000, 'prod_69a9b46d9dac1.jpg', 0),
(10, 'Futbol to''pi Adidas', 'Professional futbol to''pi, 5-razmer', 280000, 'prod_69a9b46d9dac1.jpg', 0),
(10, 'Tur palatka 4 kishilik', 'Suv o''tkazmaydigan palatka', 890000, 'prod_69a9b46d9dac1.jpg', 0),
(10, 'Boks qo''lqoplari', 'Professional boks qo''lqoplari, 12oz', 350000, 'prod_69a9b46d9dac1.jpg', 0),
(10, 'Badminton to''plami', '2 raketka + 3 volanчик', 180000, 'prod_69a9b46d9dac1.jpg', 0),
(10, 'Treadmill yugurish yo''li', 'Elektr yugurish yo''li, LCD ekran', 4500000, 'prod_69a9b46d9dac1.jpg', 0),
(10, 'Suzish ko''zoynak', 'Anti-fog suzish ko''zoynagi', 95000, 'prod_69a9b46d9dac1.jpg', 0),
(10, 'Sport shisha 750ml', 'BPA-free sport suv idishi', 45000, 'prod_69a9b46d9dac1.jpg', 0);

-- 11. Avtomobil uchun (id=11)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(11, 'Avtomobil videoregistrator', 'Full HD, GPS, kechgi rejim', 450000, 'prod_69a9b46d9dac1.jpg', 0),
(11, 'Motor moyi Mobil 1 5W-30 4L', 'Sintetik motor moyi', 380000, 'prod_69a9b46d9dac1.jpg', 0),
(11, 'Avtomobil shamchirosi', 'LED shamchirosi to''plami H7', 250000, 'prod_69a9b46d9dac1.jpg', 0),
(11, 'Avtomobil changyutgichi', 'Portativ avto changyutkich', 280000, 'prod_69a9b46d9dac1.jpg', 0),
(11, 'Telefon tutgich magnitli', 'Shamol teshigiga mahkamlash', 65000, 'prod_69a9b46d9dac1.jpg', 0),
(11, 'Avto parfyum', 'Xushbo''y hidli avto atir', 35000, 'prod_69a9b46d9dac1.jpg', 0),
(11, 'Kompressor 12V', 'Portativ shina kompressori', 320000, 'prod_69a9b46d9dac1.jpg', 0),
(11, 'Avto o''rindiq qoplama to''plami', 'Universal o''rindiq qoplamasi', 450000, 'prod_69a9b46d9dac1.jpg', 0),
(11, 'Antifreeze 5L', 'Sovutish suvi, -40°C', 120000, 'prod_69a9b46d9dac1.jpg', 0),
(11, 'Avto yuvish to''plami', 'Shampun + shimgich + cho''tka', 150000, 'prod_69a9b46d9dac1.jpg', 0);

-- 12. Kitoblar (id=12)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(12, 'O''tkan kunlar - A.Qodiriy', 'O''zbek adabiyoti klassikasi', 45000, 'prod_69a9b46d9dac1.jpg', 0),
(12, 'Atomic Habits - James Clear', 'Odatlar haqida bestseller', 85000, 'prod_69a9b46d9dac1.jpg', 0),
(12, 'Harry Potter to''plami 7 kitob', 'J.K.Rowling, o''zbek tilida', 350000, 'prod_69a9b46d9dac1.jpg', 0),
(12, 'Python dasturlash', 'Yangi boshlanuvchilar uchun qo''llanma', 95000, 'prod_69a9b46d9dac1.jpg', 0),
(12, 'Ikki eshik orasi - Tohir Malik', 'Mashhur roman', 55000, 'prod_69a9b46d9dac1.jpg', 0),
(12, 'Rich Dad Poor Dad', 'Moliyaviy savodxonlik kitobi', 75000, 'prod_69a9b46d9dac1.jpg', 0),
(12, 'Ingliz tili grammatikasi', 'Murphy Essential Grammar', 120000, 'prod_69a9b46d9dac1.jpg', 0),
(12, 'Bolalar entsiklopediyasi', 'Rangli rasmlar bilan, 500 bet', 180000, 'prod_69a9b46d9dac1.jpg', 0),
(12, 'Alximik - Paulo Koelo', 'Dunyoga mashhur roman', 65000, 'prod_69a9b46d9dac1.jpg', 0),
(12, '48 Qonun - Robert Grin', 'Hokimiyat qonunlari haqida', 110000, 'prod_69a9b46d9dac1.jpg', 0);

-- 13. Kompyuter texnikasi (id=13)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(13, 'Monitor Samsung 27" IPS', '2K, 75Hz, HDMI', 3200000, 'prod_69a9b46d9dac1.jpg', 0),
(13, 'Logitech simsiz klaviatura', 'Bluetooth, qora rang', 450000, 'prod_69a9b46d9dac1.jpg', 0),
(13, 'Razer gaming sichqoncha', 'RGB, 16000 DPI', 580000, 'prod_69a9b46d9dac1.jpg', 0),
(13, 'Kingston SSD 500GB', 'NVMe M.2 SSD disk', 650000, 'prod_69a9b46d9dac1.jpg', 0),
(13, 'Corsair RAM 16GB DDR4', '3200MHz, 2x8GB to''plam', 720000, 'prod_69a9b46d9dac1.jpg', 0),
(13, 'Logitech C920 veb kamera', 'Full HD, mikrofon bilan', 680000, 'prod_69a9b46d9dac1.jpg', 0),
(13, 'TP-Link Wi-Fi router', 'Dual Band, AC1200', 320000, 'prod_69a9b46d9dac1.jpg', 0),
(13, 'USB flesh haydovchi 128GB', 'Samsung USB 3.0', 95000, 'prod_69a9b46d9dac1.jpg', 0),
(13, 'Gaming stul', 'Ergonomik gaming stul, qora-qizil', 2800000, 'prod_69a9b46d9dac1.jpg', 0),
(13, 'UPS 1000VA', 'Uzluksiz quvvat manbai', 850000, 'prod_69a9b46d9dac1.jpg', 0);

-- 14. Telefon aksessuarlari (id=14)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(14, 'iPhone 15 Pro g''ilof', 'Silikon himoya g''ilofi', 65000, 'prod_69a9b46d9dac1.jpg', 0),
(14, 'Samsung tez zaryadlovchi 25W', 'USB-C tez zaryadlovchi', 85000, 'prod_69a9b46d9dac1.jpg', 0),
(14, 'Ekran himoya oynasi Samsung', '9H qattiqlik, anti-fingerprint', 25000, 'prod_69a9b46d9dac1.jpg', 0),
(14, 'Simsiz zaryadlovchi Xiaomi', '15W Qi simsiz zaryadlovchi', 120000, 'prod_69a9b46d9dac1.jpg', 0),
(14, 'Power Bank 20000mAh', 'Tez zaryadlash, 2 USB port', 180000, 'prod_69a9b46d9dac1.jpg', 0),
(14, 'Bluetooth quloqchin TWS', 'Simsiz quloqchin, zaryadlash qutisi', 150000, 'prod_69a9b46d9dac1.jpg', 0),
(14, 'Avtomobil telefon zaryadlovchi', '2 USB port, 3.1A', 45000, 'prod_69a9b46d9dac1.jpg', 0),
(14, 'Telefon shtativ', 'Flexible selfi shtativ', 95000, 'prod_69a9b46d9dac1.jpg', 0),
(14, 'USB-C kabel 2m', 'Tez zaryadlash kabeli', 25000, 'prod_69a9b46d9dac1.jpg', 0),
(14, 'Pop Socket tutqich', 'Telefon tutqichi, turli dizaynlar', 15000, 'prod_69a9b46d9dac1.jpg', 0);

-- 15. Oshxona jihozlari (id=15)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(15, 'Tefal qozon to''plami 5 dona', 'Yopishmaydigan qoplama', 850000, 'prod_69a9b46d9dac1.jpg', 0),
(15, 'Pichoq to''plami 6 dona', 'Zanglamaydigan po''lat pichoqlar', 320000, 'prod_69a9b46d9dac1.jpg', 0),
(15, 'Elektr choynak 1.7L', 'Tez qaynaydigan, po''lat', 250000, 'prod_69a9b46d9dac1.jpg', 0),
(15, 'Blender Philips 1.5L', 'Stol blenderi, 600W', 420000, 'prod_69a9b46d9dac1.jpg', 0),
(15, 'Piyola to''plami 6 dona', 'O''zbek piyolalari, guldor', 120000, 'prod_69a9b46d9dac1.jpg', 0),
(15, 'Toster 2 bo''limli', 'Avtomatik o''chirish funksiyali', 180000, 'prod_69a9b46d9dac1.jpg', 0),
(15, 'Choy serviz 12 dona', 'Chinni choy to''plami', 450000, 'prod_69a9b46d9dac1.jpg', 0),
(15, 'Go''sht maydalagich', 'Elektr go''sht maydalagich, 1500W', 680000, 'prod_69a9b46d9dac1.jpg', 0),
(15, 'Idish qurugich', 'Ikki qavatli idish qurugichi', 95000, 'prod_69a9b46d9dac1.jpg', 0),
(15, 'Kastrulka to''plami 3 dona', 'Zanglamaydigan po''lat, turli hajm', 550000, 'prod_69a9b46d9dac1.jpg', 0);

-- 16. Qurilish va ta'mirlash (id=16)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(16, 'Bosch burg''ulash mashina', 'Akkumulyatorli, 18V', 1200000, 'prod_69a9b46d9dac1.jpg', 0),
(16, 'Bo''yoq 10L oq', 'Suv emulsiya bo''yoq', 250000, 'prod_69a9b46d9dac1.jpg', 0),
(16, 'Asboblar to''plami 120 dona', 'Universal asboblar to''plami', 650000, 'prod_69a9b46d9dac1.jpg', 0),
(16, 'Laminat pol 1m²', 'Yog''och ko''rinishli laminat', 85000, 'prod_69a9b46d9dac1.jpg', 0),
(16, 'Sement 50kg', 'M400 qurilish sementi', 65000, 'prod_69a9b46d9dac1.jpg', 0),
(16, 'Elektr lobzik Makita', 'Professional lobzik, 650W', 780000, 'prod_69a9b46d9dac1.jpg', 0),
(16, 'Suv krani aralashtirgich', 'Oshxona uchun zamonaviy kran', 280000, 'prod_69a9b46d9dac1.jpg', 0),
(16, 'Rozetka va vyklyuchatel to''plami', 'Zamonaviy dizayn, oq rang', 45000, 'prod_69a9b46d9dac1.jpg', 0),
(16, 'Kafel plitka 1m²', 'Hammom uchun, oq rang', 95000, 'prod_69a9b46d9dac1.jpg', 0),
(16, 'Montaj ko''pik 750ml', 'Professional montaj ko''pigi', 38000, 'prod_69a9b46d9dac1.jpg', 0);

-- 17. Pet shop (id=17)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(17, 'It ozuqasi Royal Canin 15kg', 'Katta yoshdagi itlar uchun', 650000, 'prod_69a9b46d9dac1.jpg', 0),
(17, 'Mushuk ozuqasi Whiskas 5kg', 'Baliq mazali mushuk ozuqasi', 280000, 'prod_69a9b46d9dac1.jpg', 0),
(17, 'Akvarium 50L', 'Yoritgich va filtr bilan', 450000, 'prod_69a9b46d9dac1.jpg', 0),
(17, 'Mushuk tualet qutisi', 'Yopiq mushuk tualet', 180000, 'prod_69a9b46d9dac1.jpg', 0),
(17, 'It bo''yinbog''i va tasma', 'Charm bo''yinbog''i + 1.5m tasma', 120000, 'prod_69a9b46d9dac1.jpg', 0),
(17, 'Pet shampun 500ml', 'Uy hayvonlari uchun shampun', 55000, 'prod_69a9b46d9dac1.jpg', 0),
(17, 'Mushuk o''yin daraxti', 'Ko''p qavatli o''yin joyi', 350000, 'prod_69a9b46d9dac1.jpg', 0),
(17, 'It yotoq joyi', 'Yumshoq it yotoq joyi, L o''lcham', 220000, 'prod_69a9b46d9dac1.jpg', 0),
(17, 'Baliq ozuqasi 100g', 'Akvarium baliqlari uchun', 25000, 'prod_69a9b46d9dac1.jpg', 0),
(17, 'Hayvon tashish sumka', 'Portativ tashish sumkasi', 280000, 'prod_69a9b46d9dac1.jpg', 0);

-- 18. Kantselyariya (id=18)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(18, 'Parker ruchka', 'Premium yozuv ruchkasi, qora', 250000, 'prod_69a9b46d9dac1.jpg', 0),
(18, 'A4 qog''oz 500 varaq', 'Printer qog''ozi, 80g/m²', 55000, 'prod_69a9b46d9dac1.jpg', 0),
(18, 'Daftar to''plami 5 dona', 'A5 96 varaqli daftarlar', 45000, 'prod_69a9b46d9dac1.jpg', 0),
(18, 'Rangli qalamlar 48 dona', 'Professional rangli qalamlar', 85000, 'prod_69a9b46d9dac1.jpg', 0),
(18, 'Stapler + skobi', 'Ofis stapleri, 24/6', 35000, 'prod_69a9b46d9dac1.jpg', 0),
(18, 'Hujjat papka 10 dona', 'Shaffof fayl papkalar', 28000, 'prod_69a9b46d9dac1.jpg', 0),
(18, 'Kalkulyator Casio', '12 xonali ilmiy kalkulyator', 120000, 'prod_69a9b46d9dac1.jpg', 0),
(18, 'Marker to''plami 4 rang', 'Doska uchun markerlar', 32000, 'prod_69a9b46d9dac1.jpg', 0),
(18, 'Yelim stik 8 dona', 'Kleyevoy pistolet uchun', 18000, 'prod_69a9b46d9dac1.jpg', 0),
(18, 'O''chirg''ich va qalam yo''nagich', 'Yuqori sifatli to''plam', 12000, 'prod_69a9b46d9dac1.jpg', 0);

-- 19. Soatlar (id=19)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(19, 'Casio G-Shock', 'Suv o''tkazmaydigan sport soat', 1200000, 'prod_69a9b46d9dac1.jpg', 0),
(19, 'Apple Watch Series 9', 'GPS, 45mm, smart soat', 6500000, 'prod_69a9b46d9dac1.jpg', 0),
(19, 'Samsung Galaxy Watch 6', 'Bluetooth, 44mm, qora', 4200000, 'prod_69a9b46d9dac1.jpg', 0),
(19, 'Rolex Submariner (nusxa)', 'Yuqori sifatli nusxa, po''lat', 850000, 'prod_69a9b46d9dac1.jpg', 0),
(19, 'Xiaomi Smart Band 8', 'Fitness trekker, AMOLED ekran', 450000, 'prod_69a9b46d9dac1.jpg', 0),
(19, 'Daniel Wellington klassik', 'Erkaklar klassik soati, charm tasma', 1800000, 'prod_69a9b46d9dac1.jpg', 0),
(19, 'Fossil ayollar soati', 'Oltin rangdagi ayollar soati', 1500000, 'prod_69a9b46d9dac1.jpg', 0),
(19, 'Amazfit GTR 4', 'Sport smart soat, GPS, SpO2', 2800000, 'prod_69a9b46d9dac1.jpg', 0),
(19, 'Skmei suv o''tkazmaydigan soat', 'Sport soat, LED ko''rsatgich', 180000, 'prod_69a9b46d9dac1.jpg', 0),
(19, 'Devor soati zamonaviy', 'Zamonaviy uy devor soati, 30sm', 250000, 'prod_69a9b46d9dac1.jpg', 0);

-- 20. Sumkalar (id=20)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(20, 'Ayollar charm sumka', 'Tabiiy charm, jigarrang', 850000, 'prod_69a9b46d9dac1.jpg', 0),
(20, 'Erkaklar biznes sumka', 'Noutbuk uchun charm portfel', 650000, 'prod_69a9b46d9dac1.jpg', 0),
(20, 'Maktab ryukzagi', 'Ortopedik orqa, ko''k rang', 320000, 'prod_69a9b46d9dac1.jpg', 0),
(20, 'Sport sumka Nike', 'Katta sport sumkasi', 450000, 'prod_69a9b46d9dac1.jpg', 0),
(20, 'Ayollar kichik klatch', 'Kechki chiqish uchun klatch', 280000, 'prod_69a9b46d9dac1.jpg', 0),
(20, 'Travel chemodani 24"', 'Mustahkam sayohat chamadoni', 980000, 'prod_69a9b46d9dac1.jpg', 0),
(20, 'Laptop ryukzak 15.6"', 'Suv o''tkazmaydigan laptop ryukzagi', 380000, 'prod_69a9b46d9dac1.jpg', 0),
(20, 'Ayollar shopping sumka', 'Katta hajmli xarid sumkasi', 120000, 'prod_69a9b46d9dac1.jpg', 0),
(20, 'Erkaklar bel sumkasi', 'Sport bel sumkasi', 150000, 'prod_69a9b46d9dac1.jpg', 0),
(20, 'Bolalar ryukzagi', 'Bolalar uchun multfilm dizayn', 180000, 'prod_69a9b46d9dac1.jpg', 0);

-- 21. O'yinlar va konsol (id=21)
INSERT INTO products (category_id, name, description, price, image, has_sizes) VALUES
(21, 'PlayStation 5', 'Sony PS5, Disk versiya', 8500000, 'prod_69a9b46d9dac1.jpg', 0),
(21, 'Xbox Series X', 'Microsoft Xbox, 1TB SSD', 7800000, 'prod_69a9b46d9dac1.jpg', 0),
(21, 'Nintendo Switch OLED', 'Portativ o''yin konsoli', 5200000, 'prod_69a9b46d9dac1.jpg', 0),
(21, 'PS5 DualSense joystik', 'Simsiz geympad, oq rang', 950000, 'prod_69a9b46d9dac1.jpg', 0),
(21, 'GTA 5 PS5 o''yini', 'Grand Theft Auto V, PS5 versiya', 650000, 'prod_69a9b46d9dac1.jpg', 0),
(21, 'FIFA 24 PS5', 'Futbol o''yini, PS5 versiya', 750000, 'prod_69a9b46d9dac1.jpg', 0),
(21, 'Gaming garnitura HyperX', '7.1 surround, mikrofon bilan', 850000, 'prod_69a9b46d9dac1.jpg', 0),
(21, 'Xbox Game Pass 12 oylik', 'Xbox Game Pass Ultimate obuna', 1200000, 'prod_69a9b46d9dac1.jpg', 0),
(21, 'Nintendo Pro Controller', 'Professional simsiz joystik', 780000, 'prod_69a9b46d9dac1.jpg', 0),
(21, 'PS5 VR2 shlem', 'Virtual reallik shlemi', 6500000, 'prod_69a9b46d9dac1.jpg', 0);
