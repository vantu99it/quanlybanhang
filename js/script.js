const sidebar = document.querySelector("#sidebar");
const main_right = document.querySelector("#main-right");
const menu_mobile = document.querySelector(".menu-mobile");

menu_mobile.onclick = function () {
  sidebar.classList.toggle("open");
  main_right.classList.toggle("open");
  menu_mobile.classList.toggle("open");
};

$(document).ready(function () {
  showMenu();
});
function showMenu() {
  $(".sidebar-menu>li").click(function () {
    // alert("ok");
    if ($(this).hasClass("active")) {
      $(this).children(".sidebar-menu-mini").slideUp();
      $(this).removeClass("active");
    } else {
      $(".sidebar-menu-mini").slideUp();
      $(this).children(".sidebar-menu-mini").slideDown();
      $(".sidebar-menu>li").removeClass("active");
      $(this).addClass("active");
    }
  });
}

// phân trang danh sách
$(document).ready(function () {
  $("#table-manage").DataTable({
    language: {
      url: "https://cdn.datatables.net/plug-ins/1.12.1/i18n/vi.json",
    },
    pageLength: 15,
    lengthMenu: [1, 5, 15, 30, 50, 100, 200, 400, 500, 800, 1000],
    // order: [[0, "desc"]],
  });
});
// Hiện ảnh
function ImageFileAsUrl() {
  var fileSelected = document.getElementById("upload-img").files;
  // console.log(fileSelected.length);
  if (fileSelected.length > 0) {
    for (var i = 0; i < fileSelected.length; i++) {
      var fileToLoad = fileSelected[i];
      var fileReader = new FileReader();
      fileReader.onload = function (fileLoaderEvent) {
        var srcData = fileLoaderEvent.target.result;
        var newImage = document.createElement("img");
        newImage.src = srcData;
        document.getElementById("display-img").innerHTML += newImage.outerHTML;
      };
      fileReader.readAsDataURL(fileToLoad);
    }
  }
}
function ImageFileAsUrlUpdate() {
  var fileSelected = document.getElementById("upload-img-Update").files;
  // console.log(fileSelected.length);
  if (fileSelected.length > 0) {
    for (var i = 0; i < fileSelected.length; i++) {
      var fileToLoad = fileSelected[i];
      var fileReader = new FileReader();
      fileReader.onload = function (fileLoaderEvent) {
        var srcData = fileLoaderEvent.target.result;
        var newImage = document.createElement("img");
        newImage.src = srcData;
        document.getElementById("display-img-Update").innerHTML +=
          newImage.outerHTML;
      };
      fileReader.readAsDataURL(fileToLoad);
    }
  }
}
// form-act
$(document).ready(function () {
  // Nhấn nút thêm mới
  $(".btn-add").click(function () {
    $(".form-act").fadeIn(500);
  });
  // Nhấn dấu x tắt form
  $(".form-close").click(function () {
    $(".form-act").fadeOut(500);
  });
});

$(document).ready(function () {
  $(".post-button").click(function () {
    var id_detail = $(this).data("id");
    $.ajax({
      url: "./include/get-sell-manager-detail.php?id_detail=" + id_detail,
      success: function (response) {
        $("#result").html(response);
        $("#result").addClass("active");
      },
      error: function (xhr) {
        console.log(xhr.responseText);
      },
    });
  });
});
$(document).ready(function () {
  $(".post-button-cod").click(function () {
    var id_cod = $(this).data("id");
    $.ajax({
      url: "./include/get-code-detail.php?id_cod=" + id_cod,
      success: function (response) {
        $("#result").html(response);
        $("#result").addClass("active");
      },
      error: function (xhr) {
        console.log(xhr.responseText);
      },
    });
  });
});

document.addEventListener("DOMContentLoaded", function () {
  var filterButton = document.querySelector(".filter-btn");
  var filterSection = document.querySelector(".filter-none");

  filterButton.addEventListener("click", function () {
    filterSection.classList.toggle("open");
    filterButton.classList.toggle("open");
  });
});

$(document).ready(function () {
  $(".question").click(function (event) {
    var questionInfo = $(this).find(".question-info");
    if (questionInfo.is(":visible")) {
      questionInfo.hide();
    } else {
      questionInfo.show();
    }
    event.stopPropagation();
  });

  $(document).click(function (event) {
    if (!$(event.target).closest(".question").length) {
      $(".question-info").hide();
    }
  });
});

document.addEventListener("DOMContentLoaded", function () {
  var resultElement = document.getElementById("result");
  if (resultElement) {
    resultElement.addEventListener("click", function (event) {
      if (!event.target.closest("table")) {
        this.classList.remove("active");
      }
    });
  }
});
