$(function () {
    $.nette.init();
});

$(document).ready(function () {
    $("select").chosen({
        no_results_text: "Nenalezeno",
        placeholder_text_multiple: "Vyberte z možností",
        search_contains: true,
    });

    $("[data-toggle='tooltip']").tooltip();

    $("[type='checkbox']").after("<span class='custom-style-checkbox'></span>");
    $("[type='radio']").after("<span class='custom-style-radio'></span>");

    $("#duration").mask("AA:BA:BA", {"translation": {
            A: {pattern: /[0-9]/},
            B: {pattern: /[0-5]/},
        }
    });

    // sort overviews by parameters
    var url = $(location).attr("pathname");
    var searchExistingParams = new URLSearchParams(window.location.search);
    if (searchExistingParams.has("page")) {
        var page = searchExistingParams.get("page");
    }
    $("#sorter").on("change", function () {
        var sortBy = $("#sorter").val().split("-");
        var sortParams = jQuery.param({ sortBy: sortBy[0], direction: sortBy[1] });
        $.nette.ajax({
            url: url,
            type: "GET",
            data: { sortParams },
            success: function () {
                if (page) {
                    var newUrl = url + "?page=" + page + "&" + sortParams
                } else {
                    var newUrl = url + "?" + sortParams;
                }
                $(window.location.replace(newUrl));
            }
        })
    });

    // star rating component
    var star = $(".book-rating-star");
    var thumb = $(".book-rating-thumb-down");
    var ratingInput = $("#rating-input");
    var removeRatingButton = $(".book-rating-remove");

    var searchReadBookId = new URLSearchParams(window.location.search);
    if (searchReadBookId.has("readBookId")) {
        var readBookId = searchReadBookId.get("readBookId");
    }

    if (readBookId) {
        var defaultRating = ratingInput.val();
        if (defaultRating == 0) {
            defaultHighlightThumbDown();
        } else if (defaultRating > 0) {
            defaultHighlightStar();
        }
    }

    function defaultHighlightStar() {
        star.each(function (index) {
            $(this).addClass("highlight-book-rating");
            if (index == defaultRating-1) {
                return false;
            }
        });
    }

    function defaultHighlightThumbDown() {
        thumb.addClass("highlight-book-rating");
    }

    function highlightStar(obj) {
        if (ratingInput.val()) {
            return;
        }
        removeHighlightStar();
        star.each(function (index) {
            $(this).addClass("highlight-book-rating");
            if (index == star.index(obj)) {
                return false;
            }
        });
    }

    function highlightThumbDown() {
        if (ratingInput.val()) {
            return;
        }
        removeHighlightThumbDown();
        thumb.addClass("highlight-book-rating");
    }

    function removeHighlightStar() {
        if (ratingInput.val()) {
            return;
        }
        star.removeClass("selected-book-rating");
        star.removeClass("highlight-book-rating");
    }

    function removeHighlightThumbDown() {
        if (ratingInput.val()) {
            return;
        }
        thumb.removeClass("selected-book-rating");
        thumb.removeClass("highlight-book-rating");
    }

    star.on("mouseover", function () {
        highlightStar(this);
    });

    thumb.on("mouseover", function () {
        highlightThumbDown(this);
    });

    star.on("mouseout", function () {
        removeHighlightStar();
    });

    thumb.on("mouseout", function () {
        removeHighlightThumbDown();
    });

    star.on("click", function () {
        var item = $(this);
        ratingInput.val(item.data("value"));
        highlightStar(item);
    });

    thumb.on("click", function () {
        var item = $(this);
        ratingInput.val(item.data("value"));
        highlightThumbDown();
    });

    removeRatingButton.on("click", function () {
        if (defaultRating) {
            defaultRating = null;
        }
        ratingInput.val("");
        removeHighlightStar();
        removeHighlightThumbDown();
    });



});
