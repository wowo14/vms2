(function ($) {
  "use strict";
  function hitung() {
    $("tr").each(function () {
      var sum = 0;
      var sum2 = 0;
      $(this)
        .find(".unitsum")
        .each(function () {
          var unitsum = $(this).text();
          if (!isNaN(unitsum) && unitsum.length !== 0) {
            sum += parseFloat(unitsum);
          }
        });
      $(this)
        .find(".footer-sum")
        .each(function () {
          var footersum = $(this).text();
          if (!isNaN(footersum) && footersum.length !== 0) {
            sum2 += parseFloat(footersum);
          }
        });
      $(this)
        .find(".total-unitsum")
        .html(
          Math.round(sum + "e+2") + "e-2"
          //Math.round(sum + "e+2")  + "e-2"
        );
      $(this)
        .find(".total-footer-sum")
        .html(Math.round(sum2 + "e+2") + "e-2");
    });
  }
  function formatCurrency() {
    $(".auto").autoNumeric("init", {
      aSign: "Rp. ",
      aDec: ",",
      mDec: "2",
      aSep: ".",
      maximumValue: "10000000000000",
      minimumValue: "-10000000000000",
      vMax: "10000000000000",
      vMin: "-10000000000000",
    });
    $(".auto").css("text-align", "right");
  }
  function sumColumn() {
    var i = 0;
    for (i = 0; i < $("tbody tr:eq(0) td").length; i++) {
      var total = 0;
      $("td.unitsum:eq(" + i + ")", "tr").each(function (i) {
        total = total + parseInt($(this).text());
      });
      $(".footer-sum").eq(i).text(total);
    }
  }
  function reverseFormat(lang, currency, money) {
    const separatorDecimal = new Intl.NumberFormat(lang, {
      style: "decimal",
    })
      .format(11.11)
      .replace(/\d/g, "");
    const separatorThousands = new Intl.NumberFormat(lang, {
      style: "decimal",
    })
      .format(1111)
      .replace(/\d/g, "");
    const symbolOnLeft = new Intl.NumberFormat(lang, {
      style: "currency",
      currency,
    })
      .format(1)
      .replace(
        new RegExp(`\\d|[${separatorDecimal}${separatorThousands}]*`, "g"),
        ""
      );
    const stringNumber = money
      .replace(new RegExp(`[${separatorThousands}]`, "g"), "")
      .replace(separatorDecimal, ".")
      .replace(new RegExp(`[${symbolOnLeft}]`, "g"), "");
    return parseFloat(stringNumber);
  }
  sumColumn();
  hitung();
  formatCurrency();
  $(".auto").autoNumeric("init", {
    aSign: "Rp. ",
    aDec: ",",
    aSep: ".",
    maximumValue: "10000000000000",
    minimumValue: "-10000000000000",
    vMax: "10000000000000",
    vMin: "-10000000000000",
  });
  $(".auto").css("text-align", "right");
})(jQuery);
function setupImagePreview(inputElement, imagePreviewElement, targetElement) {
  // also pdf
  inputElement.on("change", function (e) {
    const input = e.target;
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function (e) {
        const preview = imagePreviewElement;
        preview.empty();
        const file = input.files[0];
        const extension = file.name.split(".").pop().toLowerCase();
        if (extension === "pdf") {
          const pdfEmbed = $(
            "<embed style='width:60%;height:400px;' src='" +
              e.target.result +
              "' type='application/pdf'>"
          );
          targetElement.val(e.target.result);
          preview.append(pdfEmbed);
        } else if (extension.match(/(jpg|jpeg|png|gif)$/)) {
          const image = $(
            "<img style='width:60%;' src='" + e.target.result + "'>"
          );
          targetElement.val(e.target.result);
          preview.append(image);
        } else {
          preview.text("Unsupported file type");
        }
      };
      reader.readAsDataURL(input.files[0]);
    }
  });
}
function formatNumber(num) {
  return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
}
function setid(el, target) {
  var dd = $(el).attr("id");
  newArr = dd.split("-");
  newArr[3] = target;
  return newArr.join("-");
}
function reverseCurrency(value) {
  value = value.replace(/Rp\s?/g, "");
  value = value.replace(/\./g, "");
  value = value.replace(",", ".");
  return parseFloat(value);
}
// Call the function with the specific elements
// setupImagePreview($("#imageInput"), $("#imagePreview"), $("#file_akta"));
