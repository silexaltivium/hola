//******************************
// New folder insertion
//******************************

function newfolder(c, d,opts, b) {
	//Insert into folder management
	$('<div class="folder" id="folder-' + b.id + '"><a href="#" title="Delete" class="remove"><span class="icon trash"></span></a>' + b.name + '<!--Delete--><form class="on-delete-folder" method="post"><input type="hidden" name="value" value="' + b.id + '"><input type="hidden" name="s" value="folders"></form><!--End .edit--></div></div>').prependTo(c);
	//Insert into folder navigation
	$('<a href="#' + b.name + '" class="item folder" id="folderview-' + b.id + '"><span class="icon folder"></span>' + b.name + '</a>').prependTo(d);
	//Insert folder into folder options
	$('<option id="folderopt-'+b.id+'" selected="selected" value="'+b.id+'">'+b.name+'</option>').prependTo(opts);
}
//******************************
// Upload on file select
//******************************

function upload(b) {
	var a = $.event.fix(b).target;
	$(a).closest("form").submit()
}
//******************************
// Shake animation
//******************************
$.fn.shake = function () {
	$(this).effect("shake", {
		distance: 10,
		times: 2
	}, 35)
};
//******************************
// New upload insertion
//******************************
function newupload(c, b) {
	var a = "";
	if(b.thumb) {
		a = '<img src="' + b.thumb + '">'
	}
	$('<!--File--><div class="file ' + b.type.toLowerCase() + ' folder-' + b.folder + '" id="' + b.url + '"><input type="checkbox" class="select"><span class="icon delete"></span><form class="on-delete" method="post"><input type="hidden" name="value" value="' + b.url + '"></form><div class="inner filetype ' + b.type.toLowerCase() + '">' + a + '<!--Toolbar--><div class="tools"><a href="#" class="item" title="0 download(s)"><span class="icon downloads"></span>0</a><a href="#" class="item lock" title="Set password"><span class="icon lock"></a><a href="#" class="item link" title="Copy link"><span class="icon link"></a><a class="shortlink" target="_blank" href="'+b.shorturl+'">'+b.shorturl+'</a><form class="on-password" method="post"><input type="hidden" name="value" value="' + b.url + '"><input type="text" name="password" autocomplete="off" value=""></form><!--End tools--></div></div><div class="title"><a href="' + b.target + '">' + b.name + "</a></div></div>").prependTo(c).hide();
	var d = "#" + b.url;
	$(d).animate({
		width: "show",
		opacity: "show"
	}, 700);
	$("#nouploads").remove()
}
//******************************
// New user insertion
//******************************

function newuser(c, b) {
	var a = "";
	if (b.admin) {
		a = 'checked="checked"'
	}
	$('<div class="user" id="user-' + b.id + '"><a href="#" title="Delete" class="remove"><span class="icon trash"></span></a>' + b.name + '<div class="edit" style="display: none;"><!--Update--><form class="on-update-user" method="post"><input type="hidden" name="value" value="' + b.id + '"><input type="hidden" name="s" value="users"><input type="submit" name="action" value="update" class="submit"><label for="name" class="text">Maxspace:<input type="text" name="maxupload" class="text" value="' + b.maxupload + '"></label><label for="admin">Is admin:<input ' + a + ' type="checkbox" name="admin" value="1" class="check"></label></form><!--Delete--><form class="on-delete-user" method="post"><input type="hidden" name="value" value="' + b.id + '"><input type="hidden" name="s" value="users"></form><!--End .edit--></div></div>').prependTo(c)
}
//******************************
// Update used space
//******************************

function updatespace() {
	$.ajax({
		type: "POST",
		url: path + "manage/",
		data: "action=space",
		success: function (a) {
			$("#space").text(a)
		}
	})
}
//******************************
// New upload submission
//******************************
$(function () {
	var currentFolder = '';
	$("#on-upload").ajaxForm({
		data: {
			ajax: "ajax"
		},
		url: path + "manage/",
		beforeSubmit: function (arr) {
			$("#loading").fadeIn("fast")
		},
		success: function (b) {
			var a = $.parseJSON(b);
			if (a.error) {
				$("#message").text(a.error).hide().removeClass("invalid").addClass("invalid").fadeIn().delay(4000).fadeOut(400);
			} else {
				newupload("#files", a)
			}
			updatespace();
			$("#loading").fadeOut("fast")
		}
	});
	//******************************
	// File delete
	//******************************	
	$(".file .delete").live("click", function () {
		var a = $(this).parent().children(".on-delete");
		$(a).ajaxSubmit({
			data: {
				ajax: "ajax",
				action: "delete"
			},
			success: function (b) {
				var c = "#" + b;
				updatespace();
				$(c).animate({
					width: "hide",
					opacity: "hide"
				}, 600, function () {
					$(c).remove()
				})
			}
		})
	});
	//******************************
	// File password set
	//******************************
	$(".on-password").live("submit", function () {
		$(this).ajaxSubmit({
			data: {
				ajax: "ajax",
				action: "password"
			},
			success: function (a) {
				$("#message").text("Password was set sucessfuly").hide().removeClass("invalid").addClass("valid").fadeIn().delay(4000).fadeOut(400)
			}
		});
		return false
	});
	//******************************
	// New user creation
	//******************************
	$("#on-create-user").ajaxForm({
		data: {
			ajax: "ajax"
		},
		dataType: "json",
		success: function (a) {
			if (a.error) {
				$("#users .message").text(a.error).hide().addClass("invalid").fadeIn();
			} else {
			 $("#users .message").text("User was created sucessfuly").hide().removeClass("invalid").addClass("valid").fadeIn();
				newuser("#userscontainer", a);
			}
		}
	});
	//******************************
	// Update user
	//******************************
	$(".on-update-user").live("submit", function () {
		$(this).ajaxSubmit({
			data: {
				ajax: "ajax",
				action: "update"
			},
			dataType: "json",
			success: function (a) {
				if (a.error) {
					$("#users .message").text(a.error).hide().addClass("invalid").fadeIn()
				} else {
					$("#users .message").text("User was updated sucessfuly").hide().removeClass("invalid").addClass("valid").fadeIn();
				}
			}
		});
		return false
	});
	//******************************
	// User password change user
	//******************************
	$("#on-password-user").ajaxForm({
		data: {
			ajax: "ajax"
		},
		dataType: "json",
		success: function (a) {
			if (a.error) {
				$("#settings .message").text(a.error).hide().addClass("invalid").fadeIn();
			}
			else {
				$("#settings .message").text("Password was changed sucessfuly").hide().removeClass("invalid").addClass("valid").fadeIn();
			}
		}
	});
	//******************************
	// Remove user
	//******************************
	$(".user .remove").live("click", function (b) {
		var a = $(this).parent().children(".edit").children(".on-delete-user");
		$(a).ajaxSubmit({
			data: {
				ajax: "ajax",
				action: "delete"
			},
			success: function (c) {
				var d = "#user-" + c;
				$(d).fadeOut(100, function () {
					$(d).remove()
				});
				$("#users .message").text("User was removed sucessfuly").hide().removeClass("invalid").addClass("valid").fadeIn()
			}
		});
		b.stopPropagation();
	});
	//******************************
	// Folder Create
	//******************************	
	$("#on-create-folder").ajaxForm({
		data: {
			ajax: "ajax"
		},
		dataType: "json",
		success: function (a) {
			if (a.error) {
				$("#folders .message").text(a.error).hide().addClass("invalid").fadeIn();
			}
			else {
				newfolder("#folderslist", "#foldersview .folders","#folderoptions", a);
				$("#folders .message").text("Folder was created sucessfuly").hide().removeClass("invalid").addClass("valid").fadeIn();
				// Hide empty message
				$('#emptyfolders').remove();
			}
		}
	});
	//******************************
	// Folder remove
	//******************************
	$(".folder .remove").live("click", function (b) {
		if (confirm("Are you sure you want to delete this folder ? All associated uploads will remain in your account.")) {
			var a = $(this).parent().children(".on-delete-folder");
			$(a).ajaxSubmit({
				data: {
					ajax: "ajax",
					action: "delete"
				},
				success: function (c) {
					var d = "#folder-" + c;
					$(d).fadeOut(100, function () {
						$(d).remove()
					});
					var v = "#folderview-" + c;
					$(v).fadeOut(100, function () {
						$(v).remove()
					});
					//Remove folder from folder options
					var folderoption = "#folderopt-" + c;
					$(folderoption).remove();
					$("#folders .message").text("Folder was removed sucessfuly").hide().removeClass("invalid").addClass("valid").fadeIn().delay(4000).fadeOut(400);
				}
			});
		}
	});
	//******************************
	// Password protected file download
	//******************************
	$("#download-lock").ajaxForm({
		data: {
			ajax: "ajax"
		},
		success: function (a) {
			if (a === "success") {
				$.download("", $("#download-lock").serialize() + "&action=get")
			} else {
				$("#fileparent").shake()
			}
		}
	});
	//******************************
	// Settings update
	//******************************
	$(".on-settings-update").ajaxForm({
		data: {
			ajax: "ajax"
		},
		dataType: "json",
		success: function (a) {
			if (a.error) {
				$("#settings .message").text(a.error).hide().addClass("invalid").fadeIn();
			}
			else {
				location.reload();
			}
		}
	});
	//******************************
	// Inerface interaction
	//******************************	
	$("#manageusers").leanModal({
		overlay: 0.7
	});
	$("#managefolders_show").leanModal({
		overlay: 0.7
	});
	$("#statistics_show").leanModal({
		overlay: 0.7
	});
	$("#settings_show").leanModal({
		overlay: 0.7
	});
	//******************************
	//User management interaction
	//******************************
	$(".user").live("click", function (a) {
		a.stopPropagation();
		$(".user .edit").removeClass("visible").addClass("hidden");
		$(".user.active").removeClass("active");
		$(this).children(".edit").removeClass("hidden").addClass("visible");
		$(this).addClass("active");		
	});
		$("html").click(function () {
		$(".user.active").removeClass("active");		
		$(".user .edit").removeClass("visible").addClass("hidden");
	});
	//******************************
	//File password toolbar interaction
	//******************************
	$(".file .tools .lock").live("click", function (b) {
		var a = $(this).closest(".file");
		$(a).children(".inner").children(".tools").children(".on-password").show();		
		//Hide link field
		$(".file .tools .shortlink").hide();
		//Show password field
		$(a).children(".inner").children(".tools").animate({
			height: "45px"
		}, 400);
		return false;
	});
	//******************************
	//File link toolbar interaction
	//******************************
	$(".file .tools .link").live("click", function (b) {
		var a = $(this).closest(".file");
		$(a).children(".inner").children(".tools").children(".shortlink").show();
		//Hide password field
		$(".file .tools .on-password").hide();
		$(a).children(".inner").children(".tools").animate({
			height: "45px"
		}, 400);
		return false;
	});
	//******************************
	//Folder navigation hide
	//******************************
	$('#foldersview a.hide').click(function () {
		$('#foldersview').fadeOut('fast');
		return false;
	});
	//******************************
	//Folder filtering
	//******************************
	$('#foldersview a.folder').live('click',function () {
		if ($(this).hasClass("active")) {
			$(this).removeClass("active");
			//Hide folder files
			$(".file").animate({
				width: "show",
				opacity: "show"
			});
			//Release current folder value
			currentFolder = '';
			//Set form input value
			$('input[name=folder]').val(currentFolder);
			return;
		}
		//Set active folder
		$("#foldersview a").removeClass("active");
		$(this).addClass("active");
		//Set current folder variable
		currentFolder = $(this).attr("id").replace("folderview-", "").toString();
		//Set form input value
		$('input[name=folder]').val(currentFolder);
		//Show folder files
		var folderid = "folder-" + currentFolder;
		$(".file", "#files").each(function () {
			if (!$(this).hasClass(folderid)) {
				$(this).animate({
					width: "hide",
					opacity: "hide"
				});
				$(this).removeClass('visible');
			} else {
				$(this).animate({
					width: "show",
					opacity: "show"
				});
				$(this).addClass('visible');
			}
		});
		return false;
	});
	//******************************
	//Folder navigation show
	//******************************
	$('#managefolders').click(function () {
		if ($(this).hasClass('active')) {
			$(this).removeClass('active');
			$('#foldersview').slideUp('fast');
		} else {
			$(this).addClass('active');
			$('#foldersview').slideDown('fast');
		}
	});
 //******************************
 //File categorory filtering
 //******************************
	$("#categories li a").click(function () {
		//Set current filter class
		$("#categories li.active").removeClass("active");
		$(this).parent("li").addClass("active");
		//Get filter value
		var a = $(this).attr("href").replace("#", "");
		//Check if folder view is set and filter
		//files within that folder only
		var folder = '';
		if (currentFolder) folder = ".folder-" + currentFolder;
		//Set search target
		var target = ".file" + folder;
		if (a == "all") {
			//Show all files
			$(target).animate({
				width: "show",
				opacity: "show"
			});
			//Add visible class to all files
			$(target).addClass('visible');
		} else {
			$(target, "#files").each(function () {
				if (!$(this).hasClass(a)) {
					//Hide files without filter match
					$(this).animate({
						width: "hide",
						opacity: "hide"
					});
					//Remove visible class to file
					$('.file').removeClass('visible');
				} else {
					//Show files with filter match
					$(this).animate({
						width: "show",
						opacity: "show"
					});
					//Add visible class to file
					$(this).addClass('visible');
				}
			})
		}
		return false;
	});
	//******************************
	// Multiple file actions
	//******************************
	$('#multiple .action').change(function () {
		var action = $(this).val();
		//Show folder list if action is requested
		if (action === 'folder')
			$('#multiple .movefolder').show();
		else
			$('#multiple .movefolder').hide();
	}); 
	//******************************
	// Multiple file selection
	//******************************
	var files = [];
	$(".file .select").live('change', function () {
		if ($(this).attr('checked')) {
			//Set selected state for file
			$(this).parent().addClass('selected');
			//Show multiple actions controller
			$('#multiple').slideDown('fast');
			//Insert file id into files array
			files.push($(this).parent().attr('id'));
		}
		else {
			//remove selected state for file
			$(this).parent().removeClass('selected');
			//Remove file id from files array
			var idx = files.indexOf($(this).parent().attr('id'));
			files.splice(idx,1);
			//Hide multple actions controller
			if ($("#files").find('.selected').length === 0) $('#multiple').slideUp('fast');
		}
		//Set selected files ids to form
		$('#multiple input[name=value]').val(files.toString());
	}); 
	//******************************
	// File Hover Interaction
	//******************************
	$(".file").live({
		mouseenter: function () {
			$(".inner", this).children(".tools").slideDown(100);
			//Show select box
			$(".select", this).show();
		},
		mouseleave: function () {
			$(".inner", this).children(".tools").slideUp(100);
			$(this).children(".inner").children(".tools").animate({
				height: "15px"
			}, 400);
			$(this).children(".inner").children(".tools").children(".on-password").hide()
			//Hide select box if it's not checked
			if (!$(this).children('.select').attr('checked')) $(".select", this).hide();
		}
	});
	//******************************
	// Input placeholders
	//******************************
	$(".placeholders input").each(function () {
		if (this.defaultValue) {
			$(this).parent().addClass("active").addClass("focus")
		}
	});
	$(".placeholders input").live("focus", function () {
		if ($(this).val() == "") {
			$(this).parent().addClass("focus")
		}
	});
	$(".placeholders input").live("keyup", function (a) {
		if ($(this).val() != "") {
			$(this).parent().addClass("active")
		} else {
			$(this).parent().removeClass("active")
		}
	});
	$(".placeholders input").live("blur", function () {
		if ($(this).val() == "") {
			$(this).parent().removeClass("focus").removeClass("active")
		}
	});
	$(".placeholders label").live("click", function () {
		$(this).siblings("input").focus()
	});
	$(".replace").live("focus", function () {
		if ($(this).val() == this.defaultValue) {
			$(this).val("").toggleClass("userText")
		}
	});
	$(".replace").live("blur", function () {
		if (!$(this).val()) {
			$(this).val(this.defaultValue).toggleClass("userText")
		}
	})
});