<?php
    date_default_timezone_set('Asia/Jakarta');
    $db = "7815696ecbf1c96e6894b779456d330e.txt";

    function getBrowser()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $browser = "N/A";

        $browsers = [
            '/msie/i' => 'Internet explorer',
            '/firefox/i' => 'Firefox',
            '/safari/i' => 'Safari',
            '/chrome/i' => 'Chrome',
            '/edge/i' => 'Edge',
            '/opera/i' => 'Opera',
            '/mobile/i' => 'Mobile browser',
        ];

        foreach ($browsers as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $browser = $value;
            }
        }

        return $browser;
    }

    function getDevice(){
        $iPod = strpos($_SERVER['HTTP_USER_AGENT'],"iPod");
        $iPhone = strpos($_SERVER['HTTP_USER_AGENT'],"iPhone");
        $iPad = strpos($_SERVER['HTTP_USER_AGENT'],"iPad");
        $android = strpos($_SERVER['HTTP_USER_AGENT'],"Android");
        file_put_contents('agent_log',$_SERVER['HTTP_USER_AGENT']);
        if($iPad||$iPhone||$iPod){
            return 'ios';
        }else if($android){
            return 'android';
        }else{
            return 'pc';
        }
    }


    if(isset($_GET['getdata'])){
        function formatDateDifference($date) {
            $now = new DateTime();
            $date = new DateTime($date);
            $interval = $now->diff($date);
        
            if ($interval->y > 0) {
                return $interval->format('%y tahun yang lalu');
            } elseif ($interval->m > 0) {
                return $interval->format('%m bulan yang lalu');
            } elseif ($interval->d > 0) {
                if ($interval->d == 1) {
                    return 'kemarin';
                }
                return $interval->format('%d hari yang lalu');
            } elseif ($interval->h > 0) {
                return $interval->format('%h jam yang lalu');
            } elseif ($interval->i > 0) {
                return $interval->format('%i menit yang lalu');
            } else {
                return 'baru saja';
            }
        }


        // Read and parse data from $db
        $data = file("$db", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Sort the data by date in descending order
        usort($data, function ($a, $b) {
            $dateA = new DateTime(explode("|", $a)[3]);
            $dateB = new DateTime(explode("|", $b)[3]);
            return $dateB <=> $dateA;
        });

        // Paginate the data (5 data points per page)
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        $perPage = 5;
        $start = ($page - 1) * $perPage;
        $dataPage = array_slice($data, $start, $perPage);

        // Create an array to hold the data
        $dataArray = [];
        foreach ($dataPage as $entry) {
            $fields = explode("|", $entry);
            $dataArray[] = [
                'image' => 'https://avatar.oxro.io/avatar.svg?name='.$fields[0].'&background=random&length=1&caps=1&fontSize=200&bold=true',
                'name' => $fields[0],
                'message' => $fields[1],
                'attendance' => $fields[2],
                'date' => formatDateDifference($fields[3])
            ];
        }

        // Return the JSON response
        header('Content-Type: application/json');
        die(json_encode($dataArray));
    }
    if(isset($_GET['process'])){
        date_default_timezone_set('Asia/Jakarta');
        $cookieName = "wedding_iota";
        $cookieValue = "true";
    
        $response = [
            "code" => 400,
            "debug" => "Invalid request",
            "message" => "Please make sure to fill all data!"
        ];
    
        if(!empty($cookie)){
            // $response = [
            //     "code" => 400,
            //     "debug" => "Invalid request",
            //     "message" => "You have already RSVP'd!"
            // ];
        }
    
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Retrieve data from the form
            $name = $_POST["name"];
            $message = $_POST["message"];
            $message = str_replace('|', ',', $message);
            $message = str_replace(["\r\n", "\r", "\n"], "<br/>", $message);
            // $message = trim(preg_replace('/\r\n|\r|\n/', '<br>', $message));
            // $message = trim(preg_replace('/\n/', '<br>', $message));
            $attending = $_POST["attending"];
            $time = date('Y-m-d H:i:s');
    
            // Set $db permission
            chmod($db, 0777);
    
            // Process the data
            $data = "$name|$message|$attending|$time\n";
            if(file_put_contents("$db", $data, FILE_APPEND)){
                setcookie($cookieName, $cookieValue, time() + 31536000, "/");
                // Respond with a success message
                $response = [
                    "code" => 200,
                    "debug" => "Success",
                    "message" => "Your data has been stored successfully!"
                ];
            }else{
                // Respond with an error message
                $response = [
                    "code" => 500,
                    "debug" => error_get_last()['message'],
                    "message" => "Something went wrong while storing your data!"
                ];
            }
        }
    
        die(json_encode($response));
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talitha & Giofahreza Wedding</title>
    <link rel="icon" type="image/x-icon" href="assets/images/logo_small.png">
    <link rel="stylesheet" href="assets/css/style.css">

    <meta property="og:title" content="Talitha & Giofahreza Wedding" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://talithafahreza.my.id" />
    <meta property="og:description" content="Tanpa mengurangi rasa hormat, kami mengundang anda untuk hadir di acara pernikahan sederhana kami." />
    <meta property="og:site_name" content="Talitha & Giofahreza Wedding" />
    <meta property="og:image" content="https://talithafahreza.my.id/assets/images/logo_small.png" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/plugins/bgset/ls.bgset.min.js" integrity="sha512-H0f3dM0T89f58GnoRxtsltBJ2LB37QllwC/Gok/xTAxBskX/8kaqjQ1bt1A1UuvtsPdz+SteSO4tVlJ39ybC+g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js" integrity="sha512-q583ppKrCRc7N5O0n2nzUiJ+suUv7Et1JGels4bXOaMFQcamPk9HjdUknZuuFjBNs7tsMuadge5k9RzdmO+1GQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/owl.carousel@2.3.4/dist/owl.carousel.min.js"></script>
    <script src="assets/js/script.js?v=1"></script>
    <?php
        if(getBrowser() == 'Safari' || getDevice() == 'ios') {
            echo '<link href="assets/css/safari.css" rel="stylesheet" type="text/css" />';
        }
    ?>
</head>

<body>
    <audio src="assets/audio/asd.mp3?v=1" id="backsound"></audio>
    <div class="circle-pause-button" id="playpause">
        <i class="fa-solid fa-play" id="play"></i>
        <i class="fa-solid fa-pause" style="display: none;" id="pause"></i>
    </div>


    <div class="splash" id="splash">
        <div class="splash-content">
            <span class="subheading">The Wedding of</span>
            <h1 class="bridegroom">Talitha & Giofahreza</h1>
            <p>
                Yth. Bapak/Ibu/Saudara/i
                <br><br><br>
                <strong id="tamu"></strong>
                <br><br><br>
                <em style='font-family: "Playfair Display", Sans-serif;''>Tanpa mengurangi rasa hormat, <br> kami mengundang anda untuk hadir di acara pernikahan sederhana kami.</em>
            </p>
            <button id="read-more">
                <i class="fa-solid fa-envelope"></i>
                BUKA UNDANGAN
            </button>
        </div>
    </div>






    
    <div class="main-content" id="main-content">
        <div class="container">
            <img src="assets/images/gallery10.jpeg" alt="Full Screen Image" class="lazyload">
            <div class="overlay"></div>
            <div class="text-container">
                <img src="assets/images/story-flowers.png" class="story-flowers lazyload"><br>
                <span class="subheading">The Wedding of</span>
                <h1 class="bridegroom">Talitha & Giofahreza</h1>

                <div id="countdown">
                    <div class="countdown-item">
                        <span id="days">00</span>
                        <span class="text">Days</span>
                    </div>
                    <div class="countdown-item">
                        <span id="hours">00</span>
                        <span class="text">Hours</span>
                    </div>
                    <div class="countdown-item">
                        <span id="minutes">00</span>
                        <span class="text">Minutes</span>
                    </div>
                    <div class="countdown-item">
                        <span id="seconds">00</span>
                        <span class="text">Seconds</span>
                    </div>
                </div>

            </div>

            <!-- <div class="mask-bottom" style="transform: rotate(-90deg); width: 100px; height: 100%; margin-left: 360px; right: unset; top: 45%;"> -->
            <div class="mask-bottom">
                <img src="assets/images/mask3.png" class="lazyload" alt="mask">
            </div>

        </div>



        <!-- bride  -->
        <div class="container bride-container">
            <div class="title">
                <img src="assets/images/bride-flower.png" class="lazyload">
                <p>Bride & Groom</p>
            </div>
            <div class="bride">
                <div class="oval-card">
                    <div class="oval-card-img">
                        <img src="assets/images/bridegroom1.jpeg" class="lazyload">
                    </div>
                    <h2>Talitha<br>Hafizhah<br>Istiadzah</h2>
                    <hr>
                    <h3>Putri Pertama</h3>
                    <p>Keluarga Bapak Rahmad Hery A.</p>
                    <img src="assets/images/roseline-black.png" class="bride-bottom-rose lazyload">
                </div>
                <span class="separator">&</span>
                <div class="oval-card">
                    <div class="oval-card-img">
                        <img src="assets/images/bridegroom2.jpeg" class="lazyload">
                    </div>
                    <h2>Giofahreza<br>Asady<br>Firdaus</h2>
                    <hr>
                    <h3>Putra Keempat</h3>
                    <p>Keluarga Bapak Helmy Yunus</p>
                    <img src="assets/images/rose-groom.png" class="bride-bottom-rose lazyload">
                </div>
            </div>
        </div>


        <!-- <div class="curv-gray flipY"></div> -->

        <div class="event-container">
            <div class="event-overlay"></div>
            <div class="event-text">
                <div class="title">
                    <span class="leaf-image" style="margin-right: 0;"><img src="assets/images/confetti.png" class="lazyload" style="transform: rotate(-25deg);" alt="Leaf"></span>
                    <h1>Event</h1>
                    <span class="leaf-image flipX"><img src="assets/images/confetti.png" class="lazyload" style="transform: rotate(25deg);" alt="Leaf"></span>
                </div>
                <!-- <h1>Date & Location</h1> -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="event-card">
                            <div class="card-container">
                                <img src="assets/images/ring.png" class="event-icon lazyload">
                                <h2>Akad</h2>
                                <table class="location">
                                    <tr>
                                        <td class="location-icon"><i class="fa-solid fa-calendar"></i></td>
                                        <td><span class="location-text">Sabtu, 16 Desember 2023</span></td>
                                    </tr>
                                    <tr>
                                        <td class="location-icon"><i class="fa-solid fa-clock"></i></td>
                                        <td><span class="location-text">Selesai</span></td>
                                    </tr>
                                    <tr>
                                        <td class="location-icon"><i class="fa-solid fa-home"></i></td>
                                        <td><span class="location-text">Masjid Agung Al-Abror</span></td>
                                    </tr>
                                    <tr>
                                        <td class="location-icon"><i class="fa-solid fa-map-marker"></i></td>
                                        <td><span class="location-text">Jl. KH. Wahid Hasyim No.6, Palraman, Dawuhan, Kec. Situbondo, Kabupaten Situbondo, Jawa Timur</span></td>
                                    </tr>
                                </table>
                                <div class="map-container">
                                    <iframe
                                        class="lazyload"
                                        width="100%"
                                        height="100%"
                                        frameborder="0" style="border:0"
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2213.001521530244!2d114.00472262271853!3d-7.707328858743719!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7291cfe9a8c81%3A0xe6273abbc8536b5e!2sMasjid%20Agung%20Al-Abror%20Situbondo!5e0!3m2!1sen!2sid!4v1697313562920!5m2!1sen!2sid"
                                        allowfullscreen>
                                    </iframe>
                                    <a class="btn btn-secondary" target="_blank" href="https://www.google.com/maps/place/Masjid+Agung+Al-Abror+Situbondo,+Jl.+KH.+Wahid+Hasyim+No.6,+Palraman,+Dawuhan,+Kec.+Situbondo,+Kabupaten+Situbondo,+Jawa+Timur+68311/@-7.707069,114.004426,16z/data=!4m6!3m5!1s0x2dd7291cfe9a8c81:0xe6273abbc8536b5e!8m2!3d-7.7070692!4d114.0044256!16s%2Fg%2F1hm6fd7gw?hl=en&gl=ID"><i class="fa fa-link"></i> Buka di Google Maps</a>
                                </div>
                                <img src="assets/images/roseleaf.png" class="event-bottom-rose lazyload">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="event-card">
                            <div class="card-container">
                                <img src="assets/images/ceremony.png" class="event-icon lazyload">
                                <h2>Resepsi</h2>
                                <table class="location">
                                    <tr>
                                        <td class="location-icon"><i class="fa-solid fa-calendar"></i></td>
                                        <td><span class="location-text">Sabtu, 16 Desember 2023</span></td>
                                    </tr>
                                    <tr>
                                        <td class="location-icon"><i class="fa-solid fa-clock"></i></td>
                                        <td><span class="location-text">06:00 Malam - 09:00 Malam</span></td>
                                    </tr>
                                    <tr>
                                        <td class="location-icon"><i class="fa-solid fa-home"></i></td>
                                        <td><span class="location-text">Legacy Eatery & Space</span></td>
                                    </tr>
                                    <tr>
                                        <td class="location-icon"><i class="fa-solid fa-map-marker"></i></td>
                                        <td><span class="location-text">Jl. Tembus Baru, Paerayaan Utara, Sumber Kolak, Kec. Panarukan, Kabupaten Situbondo, Jawa Timur</span></td>
                                    </tr>
                                </table>
                                <div class="map-container">
                                    <iframe
                                        class="lazyload"
                                        width="100%"
                                        height="100%"
                                        frameborder="0" style="border:0"
                                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.664487109943!2d114.00218187512414!3d-7.719101276481913!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd729c2b95873ab%3A0x520ac2d7d9214b5f!2sLEGACY%20EATERY%20%26%20SPACE!5e0!3m2!1sen!2sid!4v1697313605964!5m2!1sen!2sid"
                                        allowfullscreen>
                                    </iframe>
                                    <a class="btn btn-secondary" target="_blank" href="https://maps.app.goo.gl/VDYdjooCZ79tkWUt5"><i class="fa fa-link"></i> Buka di Google Maps</a>
                                </div>
                                <img src="assets/images/roseleaf.png" class="event-bottom-rose lazyload">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <img src="assets/images/mask2.png" class="lazyload" style="margin-bottom: -10px;" alt="mask">
        </div>

        <!-- gallery -->
        <div class="container gallery-container">
            <div class="title">
                <span class="leaf-image flipX"><img src="assets/images/love2.png" alt="Leaf" class="lazyload" style="transform: rotate(-20deg) scaleX(-1);"></span>
                <h1>Gallery</h1>
                <span class="leaf-image"><img src="assets/images/love2.png" alt="Leaf" class="lazyload" style="transform: rotate(20deg);"></span>
            </div>
            <p style="color:white; max-width: 400px; margin: 20px auto 50px auto;">وَمِنْ كُلِّ شَيْءٍ خَلَقْنَا زَوْجَيْنِ لَعَلَّكُمْ تَذَكَّرُونَ
                <br>
                Artinya: “Dan segala sesuatu Kami Ciptakan Berpasang-pasangan supaya kamu mengingat kebesaran Allah.” (QS Az-Zariyat: 49).
            </p>
            <div class="owl-carousel">
                <div class="item">
                    <img src="assets/images/gallery6.jpeg" alt="Image 3" class="lazyload">
                </div>
                <div class="item">
                    <img src="assets/images/gallery7.jpeg" alt="Image 3" class="lazyload">
                </div>
                <div class="item">
                    <img src="assets/images/gallery3.jpeg" alt="Image 1" class="lazyload">
                </div>
                <div class="item">
                    <img src="assets/images/gallery8.jpeg" alt="Image 1" class="lazyload">
                </div>
                <div class="item">
                    <img src="assets/images/gallery5.jpeg" alt="Image 3" class="lazyload">
                </div>
            </div>
            <!-- <br>
            <div class="owl-carousel">
                <div class="item">
                    <img src="assets/images/gallery15.jpeg" alt="Image 1" class="lazyload">
                </div>
                <div class="item">
                    <img src="assets/images/gallery15.jpeg" alt="Image 2" class="lazyload">
                </div>
                <div class="item">
                    <img src="assets/images/gallery15.jpeg" alt="Image 3" class="lazyload">
                </div>
            </div> -->
            <br>
            <div class="owl-carousel">
                <div class="item">
                    <img src="assets/images/gallery12.jpeg" alt="Image 3" class="lazyload">
                </div>
                <div class="item">
                    <img src="assets/images/gallery16.jpeg" alt="Image 3" class="lazyload">
                </div>
                <div class="item">
                    <img src="assets/images/gallery9.jpeg" alt="Image 2" class="lazyload">
                </div>
                <div class="item">
                    <img src="assets/images/gallery11.jpeg" alt="Image 3" class="lazyload">
                </div>
                <div class="item">
                    <img src="assets/images/gallery13.jpeg" alt="Image 3" class="lazyload">
                </div>
                <div class="item">
                    <img src="assets/images/gallery4.jpeg" alt="Image 2" class="lazyload">
                </div>
            </div>
        </div>

        <div class="curv-gray2"></div>
        <!-- rsvp -->
        <div class="container rsvp-container">
            <div class="rsvp-content">
                <div class="title">
                    <span class="leaf-image flipX"><img src="assets/images/leaf1.png" class="lazyload" alt="Leaf"></span>
                    <h1>Rsvp</h1>
                    <span class="leaf-image"><img src="assets/images/leaf1.png" class="lazyload" alt="Leaf"></span>
                </div>
                <p style="margin: 10px 20px;">Merupakan suatu kehormatan dan kebahagiaan bagi kami, apabila Bapak/ Ibu/ Saudara/ i berkenan hadir, untuk memberikan do'a restu kepada kedua mempelai.</p>
                <br><br>
                <form id="rsvp-form">
                    <input type="text" name="name" placeholder="Nama" required>
                    <textarea name="message" placeholder="Pesan & Do'a" required></textarea>
                    <select name="attending" required>
                        <option value="" disabled selected>-- Pilih Kehadiran --</option>
                        <option value="1">Hadir</option>
                        <option value="0">Tidak Hadir</option>
                    </select>
                    <button type="submit" class="btn btn-light" style="float:right;">Kirim <i class="fa fa-paper-plane"></i></button>
                    <br><br><br><br><br><br>
                </form>


                <div id="data-container"></div>
                <div id="pagination"></div>
            </div>
        </div>

        <!-- gift -->
        <div class="container gift-container">
            <div class="mask-top">
                <img src="assets/images/mask4.png" class="lazyload" alt="mask">
            </div>
            
            <img src="assets/images/gallery11.jpeg" class="lazyload" alt="Full Screen Image">

            <div class="overlay"></div>
            <div class="gift-text">
                <div class="title">
                    <span class="leaf-image balon"><img src="assets/images/balon.png" class="lazyload" alt="balon"></span>
                    <h1>Gift</h1>
                    <span class="leaf-image balon flipX"><img src="assets/images/balon.png" class="lazyload" alt="balon"></span>
                </div>
                <p>Berkah dan doa dari orang-orang tercinta adalah anugerah terbesar dari semuanya. Namun jika memberi merupakan tanda kasih, kami dengan senang hati menerimanya dan tentunya semakin melengkapi kebahagiaan kami.</p>
                <br>
            
                <div class="accordion">
                    <div class="accordion-item">
                        <div class="accordion-header">QRIS</div>
                        <div class="accordion-content active">
                            <img src="assets/images/qris.png" class="lazyload" alt="qris" id="qris-image"> <br>
                            <button class="btn btn-primary" id="qris-download">
                                <i class="fa fa-download"></i> Save image
                            </button>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header">BCA</div>
                        <div class="accordion-content">
                            <img src="assets/images/bca.svg" class="lazyload" alt="BCA"> <br>
                            <p>
                                GIOFAHREZA ASADY FIRDAUS
                                <br>
                                <span id="rek-bca">1210527156</span>
                            </p>
                            <div class="hidden copyMessage">Text Copied!</div>
                            <button class="btn btn-primary" id="bca-copy">
                                <i class="fa fa-copy"></i> Copy rekening
                            </button>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <div class="accordion-header">Mandiri</div>
                        <div class="accordion-content">
                            <img src="assets/images/mandiri.svg" class="lazyload" alt="Mandiri"> <br>
                            <p>
                                GIOFAHREZA ASADY FIRDAUS
                                <br>
                                <span id="rek-mandiri">1400018150988</span>
                            </p>
                            <div class="hidden copyMessage">Text Copied!</div>
                            <button class="btn btn-primary" id="mandiri-copy">
                                <i class="fa fa-copy"></i> Copy rekening
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>

</html>