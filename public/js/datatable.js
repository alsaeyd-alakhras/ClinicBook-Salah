$(document).ready(function () {
    let formatNumber = (number, min = 0) => {
        // التحقق إذا كانت القيمة فارغة أو غير صالحة كرقم
        if (number === null || number === undefined || isNaN(number)) {
            return ""; // إرجاع قيمة فارغة إذا كان الرقم غير صالح
        }
        return new Intl.NumberFormat("en-US", {
            minimumFractionDigits: min,
            maximumFractionDigits: 2,
        }).format(number);
    };
    function getActiveColumnFilters() {
        let filters = {};

        // دور على كل الفلاتر النشطة
        $('input[type="checkbox"]:checked').each(function() {
            let className = $(this).attr('class');
            let value = $(this).val();

            // تجاهل قيمة "الكل" أو أي قيم خاصة
            if (value === "الكل" || value === "all" || value === "All") {
                return; // تخطى هذا العنصر
            }

            // استخرج اسم الفيلد من الكلاس
            let fieldMatch = className.match(/(\w+)-checkbox/);
            if (fieldMatch) {
                let fieldName = fieldMatch[1];
                if (!filters[fieldName]) {
                    filters[fieldName] = [];
                }
                filters[fieldName].push(value);
            }
        });

         // إضافة فلاتر التاريخ
        let fromDate = $("#from_date").val();
        let toDate = $("#to_date").val();
        if (fromDate || toDate) {
            filters[tableId == 'executives-table' ? 'implementation_date' : 'date'] = {};
            if (fromDate) filters[tableId == 'executives-table' ? 'implementation_date' : 'date']['from'] = fromDate;
            if (toDate) filters[tableId == 'executives-table' ? 'implementation_date' : 'date']['to'] = toDate;
        }

        return filters;
    }
    function getActiveColumnFiltersExcept(excludeColumnIndex) {
        let filters = {};

        $('input[type="checkbox"]:checked').each(function() {
            let className = $(this).attr('class');
            let value = $(this).val();

            // تجاهل "الكل"
            if (value === "الكل" || value === "all" || value === "All") {
                return;
            }

            // استخراج اسم الحقل
            let fieldMatch = className.match(/(\w+)-checkbox/);
            if (fieldMatch) {
                let fieldName = fieldMatch[1];

                // تجاهل العمود الحالي
                let fieldIndex = fields.indexOf(fieldName);
                if (fieldIndex === excludeColumnIndex) {
                    return;
                }

                if (!filters[fieldName]) {
                    filters[fieldName] = [];
                }
                filters[fieldName].push(value);
            }
        });

        return filters;
    }
    const table = $("#" + tableId).DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        paging: true, // تفعيل الترقيم
        pageLength: $('#advanced-pagination').val(), // عدد الصفوف في الصفحة الواحدة
        searching: true,
        info: true, // إظهار معلومات الصفحات
        lengthChange: true, // إظهار قائمة تغيير عدد المدخلات
        layout: {
            topStart: {
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: 'تصدير Excel',
                        title: '', // تخصيص العنوان عند التصدير
                        // className: 'd-none', // إخفاء الزر الأصلي
                        exportOptions: {
                            columns: columnsCopy || [], // تحديد الأعمدة التي سيتم تصديرها (يمكن تعديلها حسب الحاجة)
                            modifier: {
                                search: 'applied', // تصدير البيانات المفلترة فقط
                                order: 'applied',  // تصدير البيانات مع الترتيب الحالي
                                page: 'all'        // تصدير جميع الصفحات المفلترة
                            }
                        }
                    }
                ]
            }
        },
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'تصدير Excel',
                className: 'd-none', // إخفاء الزر الأصلي
                exportOptions: {
                    columns: columnsCopy || [],
                    modifier: {
                        search: 'applied',
                        order: 'applied',
                        page: 'all'
                    }
                }
            }
        ],
        language: {
            url: arabicFileJson,
            // إضافة ترجمات مخصصة للـ pagination
            paginate: {
                first: "الأولى",
                last: "الأخيرة",
                next: "التالي",
                previous: "السابق",
            },
            info: "عرض _START_ إلى _END_ من أصل _TOTAL_ عنصر",
            infoEmpty: "عرض 0 إلى 0 من أصل 0 عنصر",
            infoFiltered: "(تصفية من _MAX_ عنصر إجمالي)",
            lengthMenu: "عرض _MENU_ عنصر",
            zeroRecords: "لا توجد سجلات مطابقة",
            emptyTable: "لا توجد بيانات متاحة في الجدول",
            processing: "جاري المعالجة...",
        },
        ajax: {
            url: urlIndex,
            data: function (d) {
                // إضافة تواريخ التصفية إلى الطلب المرسل
                d.from_date = $("#from_date").val();
                d.to_date = $("#to_date").val();
                d.type = $("#type").val();
                d.year = $("#year").val();

                d.column_filters = getActiveColumnFilters();

                if (typeof sortConfig !== 'undefined' && sortConfig.enabled && typeof currentSortColumn !== 'undefined' && currentSortColumn && currentSortDirection) {
                    d.sort_column = currentSortColumn;
                    d.sort_direction = currentSortDirection;
                }

                // فلترة من رابط الصفحة (سجل المساعدات)
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('project_id')) d.project_id = urlParams.get('project_id');
                if (urlParams.has('office_id')) d.office_id = urlParams.get('office_id');
                if (urlParams.has('family_id')) d.family_id = urlParams.get('family_id');
            },
            dataSrc: function(json) {
                // تحديث مجاميع tfoot
                if (json.totals) {
                    updateFooterTotals(json.totals);
                }

                return json.data;
            },
            error: function (xhr, status, error) {
                console.error("AJAX error:", status, error);
            },
        },
        columns: columnsTable,
        columnDefs: [
            { targets: 1, searchable: false, orderable: false }, // تعطيل الفرز والبحث على عمود الترقيم
        ],
        dom:
            '<"top"<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>>' +
            '<"table-responsive"t>' +
            '<"bottom"<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>>',
    });
    $("#" + tableId).one('xhr.dt', function () {
        table.one('draw.dt', function () {
            table.page('last').draw('page');
        });
    });
    $("#" + tableId).one('draw.dt', function () {
        table.page('last').draw('page');
    });
    $(document).on('change', '#advanced-pagination', function() {
        table.page.len($(this).val()).draw();
    });
    $('#year').on('change', function () {
        const year = $('#year').val();
        table.ajax.reload();
    })
    function updateFooterTotals(totals) {
        if (!SUMMABLE_COLUMNS.enabled || !totals) {
            return;
        }

        // تحديث كل عمود حسب الإعدادات
        Object.keys(SUMMABLE_COLUMNS.columns).forEach(columnName => {
            const config = SUMMABLE_COLUMNS.columns[columnName];
            const footerCell = $(`#tfoot-${columnName}`);

            if (footerCell.length && totals.hasOwnProperty(columnName)) {
                let value = totals[columnName];

                // تطبيق التنسيق حسب النوع
                if (config.format === 'currency') {
                    value = formatNumber(value, 2);
                } else if (config.format === 'number') {
                    value = formatNumber(value);
                }

                footerCell.text(value);
            }
        });

        // عرض عدد الصفوف
        const totalCountCell = $('#tfoot-edit');
        if (totalCountCell.length && totals.total_count !== undefined) {
            totalCountCell.text(totals.total_count);
        }
    }
    // دالة لتغيير نصوص الأزرار لأسهم
    function updatePaginationButtons() {
        // تغيير السابق والتالي لأسهم
        $('.dt-paging-button.previous').html('<i class="fas fa-chevron-right"></i>');
        $('.dt-paging-button.next').html('<i class="fas fa-chevron-left"></i>');
    }
    // دالة النسخ المحدثة مع معالجة الشهر والبيان
    function copySelectedRowsManually() {
        var selectedData = [];
        var columnNames = columnNamesCopy || [];

        // أسماء الأشهر بالعربية
        var arabicMonths = [
            'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
            'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
        ];

        $('.select_row:checked').each(function() {
            var rowData = table.row($(this).closest('tr')).data();
            var rowValues = [];

            if(tableId == 'executives-table'){
                columnNames.forEach(function(colName) {
                    var value = rowData[colName] || '';

                    // معالجة خاصة لحقل الشهر
                    if (colName === 'month') {
                        // استخراج الشهر من تاريخ التنفيذ
                        var implementationDate = rowData[tableId == 'executives-table' ? 'implementation_date' : 'date'] ;
                        if (implementationDate) {
                            // تحويل التاريخ إلى كائن Date
                            var dateObj = new Date(implementationDate);
                            if (!isNaN(dateObj.getTime())) {
                                // الحصول على رقم الشهر (0-11) وتحويله للعربية
                                var monthIndex = dateObj.getMonth();
                                value = arabicMonths[monthIndex];
                            } else {
                                value = '';
                            }
                        } else {
                            value = '';
                        }
                    }
                    // معالجة خاصة لحقل البيان (executive_status)
                    else if (colName === 'executive_status') {
                        if (value === 'implementation') {
                            value = 'تنفيذ';
                        } else if (value === 'receipt') {
                            value = 'قبض';
                        } else if (value === 'disbursement') {
                            value = 'صرف';
                        } else {
                            // في حالة وجود قيم أخرى، اتركها كما هي أو ضع قيمة افتراضية
                            value = value || '';
                        }
                    }
                    // معالجة عادية لباقي الحقول
                    else {
                        // تنظيف القيم من HTML
                        if (typeof value === 'string') {
                            value = value.replace(/<[^>]+>/g, '').trim();
                        }
                    }

                    rowValues.push(value);
                });
            }
            if(tableId == 'allocations-table'){
                columnNames.forEach(function(colName) {
                    var value = rowData[colName] || '';

                    // معالجة خاصة لحقل الشهر
                    if (colName === 'month') {
                        // استخراج الشهر من تاريخ التنفيذ
                        var date_allocation = rowData['date_allocation'] ;
                        if (date_allocation) {
                            // تحويل التاريخ إلى كائن Date
                            var dateObj = new Date(date_allocation);
                            if (!isNaN(dateObj.getTime())) {
                                // الحصول على رقم الشهر (0-11) وتحويله للعربية
                                var monthIndex = dateObj.getMonth();
                                value = arabicMonths[monthIndex];
                            } else {
                                value = '';
                            }
                        } else {
                            value = '';
                        }
                    }
                    else if(colName === ''){
                        value = '';
                    }
                    else if(colName === 'budget_amount_shekels'){
                        value = formatNumber(value, 2);
                    }
                    // معالجة عادية لباقي الحقول
                    else {
                        // تنظيف القيم من HTML
                        if (typeof value === 'string') {
                            value = value.replace(/<[^>]+>/g, '').trim();
                        }
                    }

                    rowValues.push(value);
                });
            }
            selectedData.push(rowValues.join('\t'));
        });

        if (selectedData.length > 0) {
            // نسخ البيانات إلى الحافظة
            var textToCopy = selectedData.join('\n');

            if (navigator.clipboard) {
                navigator.clipboard.writeText(textToCopy).then(function() {
                    toastr.success(`تم نسخ ${selectedData.length} صف بنجاح`);
                    $('.select_row').prop('checked', false);
                });
            } else {
                // طريقة بديلة للمتصفحات القديمة
                var textArea = document.createElement('textarea');
                textArea.value = textToCopy;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);

                toastr.success(`تم نسخ ${selectedData.length} صف بنجاح`);
                $('.select_row').prop('checked', false);
            }
        } else {
            toastr.warning('يرجى تحديد صف واحد على الأقل للنسخ');
        }
    }
    // نسخ وظيفة الزر إلى الزر المخصص
    $(document).on('click', '#copy-export', function () {
        copySelectedRowsManually();
    });
    $(document).on("click", "#excel-export", function () {
        table.button(".buttons-excel").trigger(); // استدعاء وظيفة الزر الأصلي
    });
    $(document).on("click", "#print-btn", function () {
        table.button(".buttons-print").trigger(); // استدعاء وظيفة الطباعة الأصلية
    });
    $("#" + tableId + "_filter").addClass("d-none");
    // جلب الداتا في checkbox
    function populateFilterOptions(columnIndex, container, name) {
        const columnName = fields[columnIndex];

        let activeFilters = getActiveColumnFiltersExcept(columnIndex);

        const urlParams = new URLSearchParams(window.location.search);
        const ajaxData = {
            from_date: $("#from_date").val(),
            to_date: $("#to_date").val(),
            type: $("#type").val(),
            active_filters: activeFilters
        };
        if ($("#year").length) ajaxData.year = $("#year").val();
        if (urlParams.has('project_id')) ajaxData.project_id = urlParams.get('project_id');
        if (urlParams.has('office_id')) ajaxData.office_id = urlParams.get('office_id');
        if (urlParams.has('family_id')) ajaxData.family_id = urlParams.get('family_id');

        $.ajax({
            url: urlFilters.replace(":column", columnName),
            data: ajaxData,
            success: function (uniqueValues) {
                uniqueValues.sort();

                const checkboxList = $(container);
                checkboxList.empty();

                uniqueValues.forEach((value) => {
                    checkboxList.append(`
                        <label style="display: block;">
                            <input type="checkbox" value="${value}" class="${name}-checkbox"> ${value}
                        </label>
                    `);
                });
            },
            error: function (xhr) {
                console.error("خطأ في تحميل خيارات الفلترة", xhr);
            }
        });
    }
    function isColumnFiltered(columnIndex) {
        const filterValue = table.column(columnIndex).search();
        return filterValue !== ""; // إذا لم يكن فارغًا، الفلترة مفعلة
    }
    function updateFilterButtonsStyle() {
        for (let i = 0; i < fields.length; i++) {
            let hasActiveFilter = false;

            if(fields[i] == '#' || fields[i] == 'edit' || fields[i] == 'delete'){
                continue; // استخدم continue مش return
            }

            // فحص إذا كان هناك checkboxes محددة لهذا الحقل
            $("." + fields[i] + "-checkbox:checked").each(function() {
                let value = $(this).val();
                if (value !== "الكل" && value !== "all" && value !== "All") {
                    hasActiveFilter = true;
                    return false; // توقف عن البحث
                }
            });

            // فحص فلتر التاريخ (بنفس طريقتك)
            if (fields[i] === 'implementation_date' || fields[i] === 'date' || fields[i] === 'distributed_at') {
                if ($("#from_date").val() || $("#to_date").val()) {
                    hasActiveFilter = true;
                }
            }

            let btnId = "#btn-filter-" + i;

            if (hasActiveFilter) {
                // الفلتر نشط - غير اللون والأيقونة
                $(btnId).removeClass("btn-secondary").addClass("btn-success");
                $(btnId + " i")
                    .removeClass("fa-solid fa-filter") // أضيف fa-solid
                    .addClass("fa-brands fa-get-pocket"); // أضيف fa-brands
            } else {
                // الفلتر غير نشط - اللون الافتراضي
                $(btnId).removeClass("btn-success").addClass("btn-secondary");
                $(btnId + " i")
                    .removeClass("fa-brands fa-get-pocket") // أضيف fa-brands
                    .addClass("fa-solid fa-filter"); // أضيف fa-solid

            }
        }
    }
    // إزالة auto-reload وتحميل عند فتح dropdown فقط
    $(document).on('show.bs.dropdown', '.enhanced-filter-dropdown', function() {

        let button = $(this).find('.btn-filter');
        let btnId = button.attr('id');
        let index = parseInt(btnId.replace('btn-filter-', ''));
        let field = fields[index];

        if(field == '#' || field == 'edit' || field == 'delete'){
            return;
        }

        let hasActiveFilter = false;
        $("." + field + "-checkbox:checked").each(function() {
            let value = $(this).val();
            if (value !== "الكل" && value !== "all" && value !== "All") {
                hasActiveFilter = true;
                return false;
            }
        });

        if(!hasActiveFilter && !isColumnFiltered(index)){
            populateFilterOptions(index, ".checkbox-list-" + index, field);
        }
    });
    // تبسيط rebuildFilters
    function rebuildFilters() {
        updateFilterButtonsStyle(); // فقط تحديث الأزرار
    }
    table.on("draw", function () {
        rebuildFilters();
        // تطبيق التغييرات في البداية
        updatePaginationButtons();
        // تغيير السابق والتالي لأسهم
        $('.dt-paging-button.previous').html('<i class="fas fa-chevron-right"></i>');
        $('.dt-paging-button.next').html('<i class="fas fa-chevron-left"></i>');
    });
    // // تطبيق الفلترة عند الضغط على زر "check"
    $(".filter-apply-btn").on("click", function () {
        let target = $(this).data("target");
        let field = $(this).data("field");
        var filterValue = $("input[name=" + field + "]").val();
        table.column(target).search(filterValue).draw();
    });
    // منع إغلاق dropdown عند النقر على input أو label
    $("th  .dropdown-menu .checkbox-list-box").on("click", function (e) {
        e.stopPropagation(); // منع انتشار الحدث
    });
    // البحث داخل الـ checkboxes
    $(document).on("input", ".search-checkbox", function () {
        let searchValue = $(this).val().toLowerCase();
        let tdIndex = $(this).data("index");
        $(".checkbox-list-" + tdIndex + " label").each(function () {
            let labelText = $(this).text().toLowerCase(); // النص داخل الـ label
            let checkbox = $(this).find("input"); // الـ checkbox داخل الـ label

            if (labelText.indexOf(searchValue) !== -1) {
                $(this).show();
            } else {
                $(this).hide();
                if (checkbox.prop("checked")) {
                    checkbox.prop("checked", false); // إذا كان الـ checkbox محددًا، قم بإلغاء تحديده
                }
            }
        });
    });
    $(document).on("change", ".all-checkbox", function () {
        let index = $(this).data("index"); // الحصول على الـ index من الـ data-index

        // التحقق من حالة الـ checkbox "الكل"
        if ($(this).prop("checked")) {
            // إذا كانت الـ checkbox "الكل" محددة، تحديد جميع الـ checkboxes الظاهرة فقط
            $(
                ".checkbox-list-" + index + ' input[type="checkbox"]:visible'
            ).prop("checked", true);
        } else {
            // إذا كانت الـ checkbox "الكل" غير محددة، إلغاء تحديد جميع الـ checkboxes الظاهرة فقط
            $(
                ".checkbox-list-" + index + ' input[type="checkbox"]:visible'
            ).prop("checked", false);
        }
    });
    function escapeRegex(value) {
        return value.replace(/[-\/\\^$*+?.()|[\]{}"'`]/g, "\\$&"); // تشمل الآن علامات الاقتباس المفردة والمزدوجة
    }
    $(document).on("click", ".filter-apply-btn-checkbox", function () {
        table.ajax.reload();
        updateClearFilterButton();
        updateFilterButtonsStyle();
        table.page('last').draw('page');
        // let target = $(this).data("target"); // استرجاع الهدف (العمود)
        // let field = $(this).data("field"); // استرجاع الحقل (اسم المشروع أو أي حقل آخر)

        // // الحصول على القيم المحددة من الـ checkboxes
        // var filterValues = [];
        // // نستخدم الكلاس المناسب بناءً على الحقل (هنا مشروع)
        // $("." + field + "-checkbox:checked").each(function () {
        //     filterValues.push($(this).val()); // إضافة القيمة المحددة
        // });
        // // إذا كانت هناك قيم محددة، نستخدمها في الفلترة
        // if (filterValues.length > 0) {
        //     // تحويل القيم إلى تعبير نمطي مع إلغاء حجز الرموز الخاصة
        //     var searchExpression = filterValues.map(escapeRegex).join("|");
        //     // تطبيق الفلترة على العمود باستخدام القيم المحددة
        //     table.column(target).search(searchExpression, true, false).draw(); // Use regex search
        //     // استخدام البحث النصي العادي (regex: false)
        // } else {
        //     // إذا لم تكن هناك قيم محددة، نعرض جميع البيانات
        //     table.column(target).search("").draw();
        // }
    });
    // تطبيق التصفية عند النقر على زر "Apply"
    $(document).on("click", "#filter-date-btn", function () {
        table.ajax.reload();
    });
    // مسح فلتر التاريخ
    $(document).on("click", "#filter-date-clear-btn", function () {
        $("#from_date").val("");
        $("#to_date").val("");
        table.ajax.reload();
        updateClearFilterButton();
    });
    // زر الفرز (لجدول المساعدات)
    $(document).on("click", ".btn-sort", function () {
        if (typeof sortConfig === 'undefined' || !sortConfig.enabled || typeof currentSortColumn === 'undefined') return;
        const field = $(this).data("sort-field");
        if (!field) return;
        if (currentSortColumn === field) {
            if (currentSortDirection === 'asc') {
                currentSortDirection = 'desc';
            } else {
                currentSortColumn = '';
                currentSortDirection = '';
            }
        } else {
            currentSortColumn = field;
            currentSortDirection = 'asc';
        }
        updateSortButtonsUI();
        table.ajax.reload();
    });
    function updateSortButtonsUI() {
        if (typeof sortConfig === 'undefined' || !sortConfig.enabled) return;
        $(".btn-sort").each(function () {
            const field = $(this).data("sort-field");
            const icon = $(this).find("i");
            icon.removeClass("fa-sort fa-sort-up fa-sort-down text-muted text-primary");
            if (currentSortColumn === field) {
                icon.addClass(currentSortDirection === 'asc' ? 'fa-sort-up text-primary' : 'fa-sort-down text-primary');
            } else {
                icon.addClass("fa-sort text-muted");
            }
        });
    }
    function hasActiveFilters() {
        let hasFilters = false;

        // فحص فلاتر الـ checkboxes
        $('input[type="checkbox"]:checked').each(function() {
            let value = $(this).val();
            if (value !== "الكل" && value !== "all" && value !== "All") {
                hasFilters = true;
                return false; // توقف عن البحث
            }
        });

        // فحص فلاتر التواريخ (بنفس طريقتك)
        if ($("#from_date").val() || $("#to_date").val()) {
            hasFilters = true;
        }

        return hasFilters;
    }
    function updateClearFilterButton() {
        if (hasActiveFilters()) {
            $("#filterBtnClear").removeClass("d-none"); // إظهار الزر
        } else {
            $("#filterBtnClear").addClass("d-none"); // إخفاء الزر
        }
    }
    $(document).on("click", "#filterBtnClear", function () {
        // مسح جميع الـ checkboxes
        $('input[type="checkbox"]').prop("checked", false);

        // مسح فلاتر التواريخ والنوع
        $("#from_date").val("");
        $("#to_date").val("");
        $("#type").val("");

        // مسح فلاتر الأعمدة
        table.columns().search("");

        // إعادة تعيين الفرز (لجدول المساعدات)
        if (typeof currentSortColumn !== 'undefined') currentSortColumn = '';
        if (typeof currentSortDirection !== 'undefined') currentSortDirection = '';
        if (typeof updateSortButtonsUI === 'function') updateSortButtonsUI();

        // إعادة تحميل الجدول
        table.ajax.reload(null, false);

        // إخفاء الزر
        $("#filterBtnClear").addClass("d-none");

        // إعادة تعيين شكل أزرار الفلاتر
        for (let i = 0; i < fields.length; i++) {
            $("#btn-filter-" + i).removeClass("btn-success").addClass("btn-secondary");
            $("#btn-filter-" + i + " i").removeClass("fa-solid fa-filter").addClass("fa-brands fa-get-pocket");
        }
    });
    // لما يتغير فلتر التاريخ أو النوع
    $("#from_date, #to_date, #type").on("change", function() {
        updateClearFilterButton();
    });
    // عند تحميل الصفحة
    $(document).ready(function() {
        updateClearFilterButton();
    });
    // عند إعادة تحميل الجدول
    table.on('xhr', function() {
        setTimeout(updateClearFilterButton, 100);
    });
    // تفويض حدث الحذف على الأزرار الديناميكية
    let deleteItemId = null;

    $(document).on("click", ".delete_row", function () {
        deleteItemId = $(this).data("id");
        $('#deleteConfirmModal').modal('show');
    });

    $(document).on("click", "#confirmDeleteBtn", function () {
        if (deleteItemId) {
            deleteRow(deleteItemId);
            $('#deleteConfirmModal').modal('hide');
            deleteItemId = null;
        }
    });
    // وظيفة الحذف
    function deleteRow(id) {
        $.ajax({
            url: urlDelete.replace(":id", id),
            method: "DELETE",
            data: {
                _token: _token,
            },
            success: function (response) {
                toastr.success("تم حذف العنصر بنجاح");
                table.ajax.reload(); // إعادة تحميل الجدول بعد الحذف
            },
            error: function (xhr, status, error) {
                console.error("AJAX error:", status, error);
                toastr.error("هنالك خطاء في عملية الحذف.");
            },
        });
    }
    $(document).on("click", "#refreshData", function () {
        table.ajax.reload();
    });

    // التعديل
    function fillFormFromResponse(response) {
        Object.keys(dataForm).forEach((key) => {
            const value = response[key] ?? ''; // لو القيمة غير موجودة
            dataForm[key] = value;

            const input = $('#' + key);
            if (input.length) {
                input.val(value).trigger('change');
            }
        });
    }
    $(document).on('click', '.edit_row', function () {
        const id = $(this).data('id'); // الحصول على ID الصف
        editExecutiveForm(id); // استدعاء وظيفة الحذف
    });
    function editExecutiveForm(id) {
        $.ajax({
            url: urlEdit.replace(':id', id),
            method: 'GET',
            success: function (response) {
                fillFormFromResponse(response);
                $('#addExecutive').remove();
                $('#update').remove();
                $('#btns_form').append(`
                    <button type="button" id="update" class="mx-2 btn btn-primary">
                        <i class="fa-solid fa-pen-to-square"></i>
                        تعديل
                    </button>
                `);
                $('.editForm').css('display','block');
                $('#editModal').modal('show');
            },
            error: function (xhr, status, error) {
                console.error('AJAX error:', status, error);
                toastr.error('هنالك خطأ في الإتصال بالسيرفر.');
            },
        })
    }
    $(document).on('click', '#update', function () {
        Object.keys(dataForm).forEach((key) => {
            if (key !== 'id') {
                const input = $('#' + key);
                if (input.length) {
                    dataForm[key] = input.val();
                }
            }
        });
        $.ajax({
            url: urlUpdate.replace(':id', dataForm.id),
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': _token
            },
            data: dataForm,
            success: function (response) {
                $('#editModal').modal('hide');
                table.ajax.reload(null, false);
                toastr.success('تم التعديل بنجاح');
            },
            error: function (xhr, status, error) {
                console.error('AJAX error:', status, error);
                toastr.error('هنالك خطأ في الإتصال بالسيرفر.');
            },
        })
    });
    $(document).on('click', '#createNew', function () {
        $.ajax({
            url: urlCreate,
            method: 'GET',
            success: function (response) {
                fillFormFromResponse(response);
                $('#addExecutive').remove();
                $('#update').remove();
                $('#btns_form').append(`
                    <button type="button" id="addExecutive" class="mx-2 btn btn-primary">
                        <i class="fa-solid fa-plus"></i>
                        أضف
                    </button>
                `);
                $('.editForm').css('display','none');
                $('#editModal').modal('show');
            },
            error: function (xhr, status, error) {
                console.error('AJAX error:', status, error);
                toastr.error('هنالك خطأ في الإتصال بالسيرفر.');
            },
        })
    });
    $(document).on('click', '#addExecutive', function () {
        const id = $(this).data('id'); // الحصول على ID الصف
        createExecutiveForm(id);
    });
    function createExecutiveForm(id){
        Object.keys(dataForm).forEach((key) => {
            if (key != 'id') {
                const input = $('#' + key);
                if (input.length) {
                    dataForm[key] = input.val();
                }
            } else {
                dataForm[key] = null;
            }
        });
        $.ajax({
            url: urlStore,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': _token
            },
            data: dataForm,
            success: function (response) {
                $('#editModal').modal('hide');
                table.ajax.reload();
                toastr.success('تم إضافة التنفيذ بنجاح');
            },
            error: function (xhr, status, error) {
                console.error('AJAX error:', status, error);
                toastr.error('هنالك خطأ في الإتصال بالسيرفر.');
            },
        })
    };
});

$(document).ready(function () {
    $(document).on("click", "#filterBtn", function () {
        let text = $(this).text();
        if (text != "تصفية") {
            $(this).text("تصفية");
        } else {
            $(this).text("إخفاء التصفية");
        }
        $(".filter-dropdown").slideToggle();
    });
    $(document).on("click", "#import_excel_btn", function () {
        $("#editEmployee").modal("hide");
        $("#import_excel").modal("show");
    });
});

$(document).ready(function () {
    let currentRow = 0;
    let currentCol = 0;

    // الحصول على الصفوف من tbody فقط
    const rows = $("#" + tableId + " tbody tr");

    // إضافة الكلاس للخلايا عند تحميل الصفحة
    highlightCell(currentRow, currentCol);

    // التنقل باستخدام الأسهم
    $(document).on("keydown", function (e) {
        // تحديث عدد الصفوف والأعمدة المرئية عند كل حركة
        const totalRows = $("#" + tableId + " tbody tr:visible").length;
        const totalCols = $("#" + tableId + " tbody tr:visible")
            .eq(0)
            .find("td").length;

        // التحقق من وجود صفوف وأعمدة لتجنب NaN
        if (totalRows === 0 || totalCols === 0) return;

        // التنقل باستخدام الأسهم
        if (e.key === "ArrowLeft") {
            if (currentCol < 32) {
                currentCol = (currentCol + 1) % totalCols;
            }
        } else if (e.key === "ArrowRight") {
            if (currentCol > 0) {
                currentCol = (currentCol - 1 + totalCols) % totalCols;
            }
        } else if (e.key === "ArrowDown") {
            currentRow = (currentRow + 1) % totalRows;
        } else if (e.key === "ArrowUp") {
            // إذا كنت في الصف الأول، لا تفعل شيئاً
            if (currentRow > 0) {
                currentRow = (currentRow - 1 + totalRows) % totalRows;
            }
        } else {
            return;
        }
        highlightCell(currentRow, currentCol);
    });

    // التحديد عند النقر المزدوج بالماوس
    $("#" + tableId + " tbody").on("dblclick", "td", function () {
        const cell = $(this);
        currentRow = cell.closest("tr").index();
        currentCol = cell.index();
        highlightCell(currentRow, currentCol);
    });

    // دالة لتحديث الخلية النشطة
    function highlightCell(row, col) {
        // استهداف الصفوف المرئية فقط
        const visibleRows = $("#" + tableId + " tbody tr:visible");
        // التحقق من وجود الصف
        if (visibleRows.length > row) {
            // تحديد الصف والخلية المطلوبة
            const targetRow = visibleRows.eq(row);
            const targetCell = targetRow.find("td").eq(col);
            if (targetCell.length) {
                // إزالة التنسيقات السابقة
                $("#" + tableId + " tbody td").removeClass("active");
                // إضافة التنسيق للخلية المطلوبة
                targetCell.addClass("active");
                targetCell.focus();
            }
        }
    }
});


// إضافة تأثيرات تفاعلية
document.addEventListener('DOMContentLoaded', function () {
    // تأثير النقر على الصفوف
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('click', function () {
            // إزالة التحديد من جميع الصفوف
            tableRows.forEach(r => {
                r.classList.remove('table-active');
                r.querySelectorAll('td').forEach(td => td.classList.remove('active'));
            });

            // إضافة التحديد للصف الحالي
            this.classList.add('table-active');
            this.querySelectorAll('td').forEach(td => td.classList.add('active'));
        });
    });

    // تأثير البحث في قوائم التصفية
    const searchInputs = document.querySelectorAll('.search-checkbox');
    searchInputs.forEach(input => {
        input.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            const checkboxList = this.closest('.enhanced-filter-menu').querySelector('.enhanced-checkbox-list');
            const labels = checkboxList.querySelectorAll('label:not(:first-child)');

            labels.forEach(label => {
                const text = label.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    label.style.display = 'flex';
                } else {
                    label.style.display = 'none';
                }
            });
        });
    });

    // تأثير "تحديد الكل"
    const allCheckboxes = document.querySelectorAll('.enhanced-checkbox-list input[value="all"]');
    allCheckboxes.forEach(allCheckbox => {
        allCheckbox.addEventListener('change', function () {
            const checkboxList = this.closest('.enhanced-checkbox-list');
            const otherCheckboxes = checkboxList.querySelectorAll('input[type="checkbox"]:not([value="all"])');

            otherCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    });

    // تأثير تطبيق التصفية
    const applyButtons = document.querySelectorAll('.enhanced-apply-btn');
    applyButtons.forEach(button => {
        button.addEventListener('click', function () {
            // إغلاق القائمة المنسدلة
            const dropdown = this.closest('.dropdown');
            const dropdownToggle = dropdown.querySelector('[data-bs-toggle="dropdown"]');

            // إضافة تأثير بصري للإشارة لتطبيق التصفية
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-check"></i>';
                bootstrap.Dropdown.getInstance(dropdownToggle)?.hide();
            }, 500);
        });
    });

    // تأثير الأزرار
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.stopPropagation();

            // تأثير النقر
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);

            if (this.classList.contains('btn-delete')) {
                if (confirm('هل أنت متأكد من حذف هذا العنصر؟')) {
                    console.log('تم الحذف');
                }
            } else if (this.classList.contains('btn-edit')) {
                console.log('تم النقر على تعديل');
            }
        });
    });

    // تأثير التمرير السلس
    const tableContainer = document.querySelector('.table-container');
    let isScrolling = false;

    tableContainer.addEventListener('scroll', function () {
        if (!isScrolling) {
            window.requestAnimationFrame(function () {
                // يمكن إضافة تأثيرات أثناء التمرير هنا
                isScrolling = false;
            });
            isScrolling = true;
        }
    });
});
