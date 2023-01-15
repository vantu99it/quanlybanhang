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
    lengthMenu: [1, 2, 3, 4, 5, 10, 15, 20, 30, 50, 100],
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
