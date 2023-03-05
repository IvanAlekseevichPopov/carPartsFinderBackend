Symfony for Platform.sh
=======================


TODO
+divide images on temporary and part related
+add collection of images to one part
+add api of collection of images with rating
+add simple partNumbers crawler
+admin panel to download valid images to internal storage by click
+add deploy configuration to new server




add simple images crawler
image resize and change type to lightest(PNG?) right after upload
add test provider for image storing
add flag for moderation to parts and images
add icon who added image(robot, user, moderator)
merge all migrations to one
image remove from part edit form in admin panel
code style checker(fixer + psalm)
simple dumb tests on container, api, admin panel


TODO отдельным запросом достать description(оттуда позже можно извлечь размеры и возможно partName)

https://webapi.autodoc.ru/api/manufacturer/5216/sparepart/95gay27430909l

{
"id": 1,
"manufacturerId": 5216,
"manufacturerName": "FEBEST",
"partName": "Сальник",
"partNumber": "95GAY27430909L",
"description": "САЛЬНИК РАСПРЕДЕЛИТЕЛЬНОГО ВАЛА 27X43X9/ Размер упаковки: (ДхШхВ) 43х43х9",
"messagesCount": 0,
"properties": [],
"minimalPrice": 152.00,
"galleryModel": {
"photoType": 1,
"imgUrls": [
"https://webapi.autodoc.ru/api/spareparts/foto/Standard/5216/95GAY27430909L/8948469",
"https://webapi.autodoc.ru/api/spareparts/foto/Standard/5216/95GAY27430909L/8948470"
]
},
"seo": {
"title": "Сальник FEBEST 95gay27430909l: фото, цена, описание, применимость. Купить в интернет-магазине Автодок",
"description": "FEBEST 95gay27430909l - Сальник",
"keywords": "Купить, FEBEST, 95gay27430909l, Сальник, автодок"
},
"mark": {
"avg": 0.0,
"cnt": 0,
"cntContent": 0,
"cnt_mark1": 0,
"cnt_mark2": 0,
"cnt_mark3": 0,
"cnt_mark4": 0,
"cnt_mark5": 0
},
"isCompatibility": 2
}
