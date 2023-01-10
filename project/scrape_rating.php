<?php
    function scrapeRating($title) {
        $title = str_replace(' ', '+', $title);

        // url of the search query
        $url = "https://www.goodreads.com/search?utf8=%E2%9C%93&query=$title";

        $html = file_get_contents($url);

        $start = stripos($html, 'class="bookTitle"');
        $end = stripos($html, '>', $offset = $start);
        $length = $end - $start;

        $substring = substr($html, $start, $length);

        $book_url_start = stripos($substring, "href=") + 6;
        $book_url_end = stripos($substring, '"', $offset = $book_url_start);
        $book_url_length = $book_url_end - $book_url_start;

        // the actual url of the book
        $book_url = 'https://www.goodreads.com'.substr($substring, $book_url_start, $book_url_length);

        $book_page = file_get_contents($book_url);

        // the div in the page with the rating and other stuff
        $rating_div_start = stripos($book_page, '<div id="bookMeta"');
        $rating_div_end = stripos($book_page, '</div>', $offset = $rating_div_start) + 6;
        $rating_div_length = $rating_div_end - $rating_div_start;

        $rating_div = substr($book_page, $rating_div_start, $rating_div_length);

        // the actual rating number
        $rating_number_start = stripos($rating_div, 'itemprop="ratingValue"') + 23;
        $rating_number_end = stripos($rating_div, '</span>', $offset = $rating_number_start);
        $rating_number_length = $rating_number_end - $rating_number_start;

        $rating = substr($rating_div, $rating_number_start, $rating_number_length);

        return [
            'page' => $book_url,
            'rating' => $rating
        ];

    }
    // echo scrapeRating('On The Origin Of Species')['rating'];
?>