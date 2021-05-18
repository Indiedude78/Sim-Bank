$(document).ready(function () {

    var $table = $("#users_table");
    console.log($table);
    $.ajax({
        type: 'GET',
        url: 'http://192.168.1.75/~smit/repo/project/api/get_users.php',
        success: function (userInfo) {

            userInfo["users"].forEach(function (item, index) {
                $table.append('<tr data-' + index + '=' + index + '></tr>');
                //console.log($())
                $("tr[data-" + index + "]").append('<td>' + (index + 1) + '</td>');
                $("tr[data-" + index + "]").append('<td>' + item.fname + '</td>');
                $("tr[data-" + index + "]").append('<td>' + item.lname + '</td>');
                $("tr[data-" + index + "]").append('<td>' + item.email + '</td>');
                $("tr[data-" + index + "]").append('<td>' + item.username + '</td>');
                if (index % 2 != 0) {
                    console.log(index);
                    $("tr[data-" + index + "]").css("background-color", "#2a2c2b52");
                }
                //$("tr[data-" + index + "]").css();
                if (item.disabled == 1) {
                    $("tr[data-" + index + "]").append('<td style="color:red;"><strong>' + "Disabled" + '</strong></td>');
                }
                else {
                    $("tr[data-" + index + "]").append('<td style="color:green;"><strong>' + "Active" + '</strong></td>');
                }
                //console.log("ran");
                //console.log(item.fname);
            });
        }
    });
});