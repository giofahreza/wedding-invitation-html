// Fix iOS background-attachment: fixed issue
var bg = document.querySelector('.event-container');
window.addEventListener('scroll', function () {
    var offset = window.scrollY;
    bg.style.backgroundPosition = 'center ' + (offset * 0.5) + 'px';
});

// Function to load and display data
function loadData(page) {
    $.ajax({
        url: '?getdata',
        method: 'GET',
        data: { page: page },
        success: function (data) {
            // Display data
            if (data.length > 0) {
                var dataContainer = $('#data-container');
                dataContainer.empty();
                data.forEach(function (item) {
                    desc = item.attendance == 1 ? "Hadir" : "Tidak Hadir";
                    icon = item.attendance == 1 ? "fa fa-check" : "fa fa-times";
                    divclass = item.attendance == 1 ? "attendance-yes" : "attendance-no";
                    dataContainer.append(`<div class="guest">
                                        <div class="guest-details">
                                            <h3>
                                                <img src="`+item.image+`" alt="Guest Image">`
                                                +item.name+`
                                                <span class="attendance-status `+divclass+`"><i class='`+icon+`'></i>`+desc+`</span>
                                            </h3>
                                            <p class="guest-date"><i class="fa fa-clock"></i>`+item.date+`</p>
                                            <p class="guest-message">`+item.message+`</p>
                                        </div>
                                    </div>
                                    `);
                });
            }

            // Display pagination
            var pagination = $('#pagination');
            pagination.empty();
            if (page > 1) {
                pagination.append('<a href="#" class="page-link btn btn-light" data-page="' + (page - 1) + '"><i class="fa fa-arrow-left"></i></a>');
            } else {
                pagination.append('<a href="javascript:void(0);" class="page-link btn btn-disabled"><i class="fa fa-arrow-left"></i></a>');
            }

            if (data.length > 0) {
                pagination.append('<a href="#" class="page-link btn btn-light" data-page="' + (page + 1) + '"><i class="fa fa-arrow-right"></i></a>');
            } else {
                pagination.append('<a href="javascript:void(0);" class="page-link btn btn-disabled"><i class="fa fa-arrow-right"></i></a>');
            }
        }
    });
}

$(document).ready(function() {
    var RSVPPage = 1; // Current page
    loadData(RSVPPage);
    const readMoreButton = $("#read-more");
    const pauseButton = $("#playpause");
    var isPlaying = false;
    var played = false;

    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }

    function playpause(){
        // Play music
        $("#backsound").prop("loop", true);
        const audio = $("#backsound")[0]; 
        if(!played){
            audio.currentTime = 39;
        }
        played=true;

        if (!isPlaying) {
            // If not playing, play the audio
            audio.play();
            $("#play").hide();
            $("#pause").show();
            isPlaying = true;
        } else {
            // If playing, pause the audio
            audio.pause();
            $("#pause").hide();
            $("#play").show();
            isPlaying = false;
        }
    }

    // Set guest name
    $("#tamu").text(getParameterByName('to') || "Tamu Undangan");

    // Update the countdown every second
    var countdownInterval = setInterval(function () {
        var weddingDate = new Date("2023-12-16T17:00:00").getTime();
        var now = new Date().getTime();
        var timeRemaining = weddingDate - now;

        var days = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
        var hours = Math.floor((timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((timeRemaining % (1000 * 60)) / 1000);

        // Display the countdown values
        $("#days").text(days.toString().padStart(2, "0"));
        $("#hours").text(hours.toString().padStart(2, "0"));
        $("#minutes").text(minutes.toString().padStart(2, "0"));
        $("#seconds").text(seconds.toString().padStart(2, "0"));

        // Check if the wedding date has passed
        if (timeRemaining < 0) {
            clearInterval(countdownInterval);
            $("#countdown").html("<p>The big day has arrived!</p>");
        }
    }, 1000);

    readMoreButton.on("click", function() {
        const splash = $("#splash");
        const mainContent = $("#main-content");

        // Play music
        playpause();

        // Add a class to initiate the CSS transition
        splash.addClass("hide-splash");
        $("body").css("overflow", "auto");

        // After the transition, hide the splash screen and show the main content
        setTimeout(function() {
            // splash.css("display", "none");
            mainContent.css("display", "block");
        }, 300); // Adjust the timing to match your CSS transition duration
    });

    pauseButton.on("click", function() {
        playpause();
    });

    // Post form data
    document.getElementById("rsvp-form").addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent the default form submission
        
        // Collect form data
        const formData = new FormData(this);

        // Set the submit button text into "Loading..."
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.value = "Loading...";

        // if ($.cookie('wedding_iota')) {
        //     alert("You have already RSVP'd!");
        // } else {
            // Send the data to the "process" script using the fetch API
            fetch("?process", {
                method: "POST",
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    return response.text();
                }
                throw new Error("Network response was not ok.");
            })
            .then(responseJSON => {
                // Check the response for success or error
                console.log("Raw RSVP form response debug : "+responseJSON);
                const responseData = JSON.parse(responseJSON);
                console.log("RSVP form response debug : "+responseData.debug);
                if (responseData.code === 200) {
                    // Success: Show an alert and reload rsvp data
                    alert(responseData.message);
                    // location.reload();
                    loadData(RSVPPage);
                } else {
                    // Error: Display the error message to the user
                    alert("Error: " + responseData.message);
                }
            })
            .catch(error => {
                console.error("There was a problem with the fetch operation:", error);
            });
        // }

        // Reset the submit button text in case of an error
        submitButton.value = "Submit";
    });

    // Pagination click event
    $('#pagination').on('click', '.page-link', function (e) {
        e.preventDefault();
        var page = $(this).data('page');
        currentPage = page;
        loadData(page);
    });

    $('.owl-carousel').owlCarousel({
        margin: 10,     // Adjust the margin as needed
        stagePadding: 50,
        pagination: false,
        loop: true,     // Set to true if you want the carousel to loop
        lazyLoad: true, // This will delay loading images until user scrolls to them
        autoplay:false,
        autoplayTimeout:5000,
        autoplayHoverPause:false,
        responsive: {
            0: {
                items: 1,  // Display one item on small screens
            },
            600: {
                items: 3,  // Display two items on medium screens
            },
            1000: {
                items: 4  // Display three items on large screens
            }
        }
    });


    // Gift function

    function copyToClipboard(elementId) {
        var aux = document.createElement("input");
        aux.setAttribute("value", document.getElementById(elementId).innerHTML);
        document.body.appendChild(aux);
        aux.select();
        document.execCommand("copy");
        document.body.removeChild(aux);
    }

    $(".accordion-header").click(function () {
        $(this).next(".accordion-content").slideToggle();
        $(".accordion-content").not($(this).next()).slideUp();
    });

    $("#qris-download").click(function () {
        const image = $("#qris-image");
        const link = document.createElement("a");
        
        link.href = image.attr("src");
        link.download = "qris.png";
        
        link.click();
    });

    const copyMessage = $(".copyMessage");
    $("#bca-copy").click(function() {
        copyToClipboard("rek-bca");
        var textToCopy = $("#rek-bca");
        navigator.clipboard.writeText(textToCopy.text());
        copyMessage.removeClass("hidden").addClass("fade-in");
        // alert('Text Copied!');
        setTimeout(function() {
            copyMessage.removeClass("fade-in").addClass("hidden");
        }, 1500);
    });
    $("#mandiri-copy").click(function() {
        copyToClipboard("rek-mandiri");
        var textToCopy = $("#rek-mandiri");
        navigator.clipboard.writeText(textToCopy.text());
        copyMessage.removeClass("hidden").addClass("fade-in");
        // alert('Text Copied!');
        setTimeout(function() {
            copyMessage.removeClass("fade-in").addClass("hidden");
        }, 1500);
    });












    // Debug app (directly access main content without splash screen)
    // readMoreButton.click();
});