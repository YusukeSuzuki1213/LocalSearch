# LocalSearch
位置情報から飲食店とクチコミを検索してくれるwebアプリケーションです。

<img title="image_1" src="https://github.com/YusukeSuzuki1213/LocalSearch/blob/master/readme_images/LocalSearch_1.JPG">
<img title="image_1" src="https://github.com/YusukeSuzuki1213/LocalSearch/blob/master/readme_images/LocalSearch_2.JPG">
<img title="image_1" src="https://github.com/YusukeSuzuki1213/LocalSearch/blob/master/readme_images/LocalSearch_3.JPG">


位置情報、飲食店の種類（和食、中華、スイーツ, etc.）、位置情報から何キロ圏内かを入力することで検索条件にあった飲食店を探してくれます。  

位置情報は住所、都道府県市町村、ランドマークなどを入力すると、それにヒットした市町村を検索してくれます。  

飲食店の検索にはYOLP Yahoo!ローカルサーチAPIを使用しており、そこから画像がある飲食店だけを取得しています。  

クチコミの取得にはYOLP クチコミ検索APIを使用しており、ローカルサーチAPIで取得した飲食店のクチコミを取得しています。
