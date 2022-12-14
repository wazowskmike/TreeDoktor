/*
 dhtmlxScheduler v.4.1.0 Stardard

 This software is covered by GPL license. You also can obtain Commercial or Enterprise license to use it in non-GPL project - please contact sales@dhtmlx.com. Usage without proper license is prohibited.

 (c) Dinamenta, UAB.
 */
!function () {
    scheduler.config.container_autoresize = !0, scheduler.config.month_day_min_height = 90;
    var e = scheduler._pre_render_events;
    scheduler._pre_render_events = function (t, s) {
        if (!scheduler.config.container_autoresize)return e.apply(this, arguments);
        var i = this.xy.bar_height, n = this._colsS.heights, a = this._colsS.heights = [0, 0, 0, 0, 0, 0, 0], r = this._els.dhx_cal_data[0];
        if (t = this._table_view ? this._pre_render_events_table(t, s) : this._pre_render_events_line(t, s), this._table_view)if (s)this._colsS.heights = n; else {
            var d = r.firstChild;
            if (d.rows) {
                for (var o = 0; o < d.rows.length; o++) {
                    if (a[o]++, a[o] * i > this._colsS.height - this.xy.month_head_height) {
                        var l = d.rows[o].cells, h = this._colsS.height - this.xy.month_head_height;
                        1 * this.config.max_month_events !== this.config.max_month_events || a[o] <= this.config.max_month_events ? h = a[o] * i : (this.config.max_month_events + 1) * i > this._colsS.height - this.xy.month_head_height && (h = (this.config.max_month_events + 1) * i);
                        for (var _ = 0; _ < l.length; _++)l[_].childNodes[1].style.height = h + "px";
                        a[o] = (a[o - 1] || 0) + l[0].offsetHeight
                    }
                    a[o] = (a[o - 1] || 0) + d.rows[o].cells[0].offsetHeight
                }
                a.unshift(0), d.parentNode.offsetHeight < d.parentNode.scrollHeight && !d._h_fix
            } else if (t.length || "visible" != this._els.dhx_multi_day[0].style.visibility || (a[0] = -1), t.length || -1 == a[0]) {
                var c = (d.parentNode.childNodes, (a[0] + 1) * i + 1 + "px");
                r.style.top = this._els.dhx_cal_navline[0].offsetHeight + this._els.dhx_cal_header[0].offsetHeight + parseInt(c, 10) + "px", r.style.height = this._obj.offsetHeight - parseInt(r.style.top, 10) - (this.xy.margin_top || 0) + "px";
                var u = this._els.dhx_multi_day[0];
                u.style.height = c, u.style.visibility = -1 == a[0] ? "hidden" : "visible", u = this._els.dhx_multi_day[1], u.style.height = c, u.style.visibility = -1 == a[0] ? "hidden" : "visible", u.className = a[0] ? "dhx_multi_day_icon" : "dhx_multi_day_icon_small", this._dy_shift = (a[0] + 1) * i, a[0] = 0
            }
        }
        return t
    };
    var t = ["dhx_cal_navline", "dhx_cal_header", "dhx_multi_day", "dhx_cal_data"], s = function (e) {
        for (var s = 0, i = 0; i < t.length; i++) {
            var n = t[i], a = scheduler._els[n] ? scheduler._els[n][0] : null, r = 0;
            switch (n) {
                case"dhx_cal_navline":
                case"dhx_cal_header":
                    r = parseInt(a.style.height, 10);
                    break;
                case"dhx_multi_day":
                    r = a ? a.offsetHeight : 0, 1 == r && (r = 0);
                    break;
                case"dhx_cal_data":
                    r = Math.max(a.offsetHeight - 1, a.scrollHeight);
                    var d = scheduler.getState().mode;
                    if ("month" == d) {
                        if (scheduler.config.month_day_min_height && !e) {
                            var o = a.getElementsByTagName("tr").length;
                            r = o * scheduler.config.month_day_min_height
                        }
                        e && (a.style.height = r + "px")
                    }
                    if (scheduler.matrix && scheduler.matrix[d])if (e)r += 2, a.style.height = r + "px"; else {
                        r = 2;
                        for (var l = scheduler.matrix[d], h = l.y_unit, _ = 0; _ < h.length; _++)r += h[_].children ? l.folder_dy || l.dy : l.dy
                    }
                    ("day" == d || "week" == d) && (r += 2)
            }
            s += r
        }
        scheduler._obj.style.height = s + "px", e || scheduler.updateView()
    }, i = function () {
        var e = scheduler.getState().mode;
        s(), (scheduler.matrix && scheduler.matrix[e] || "month" == e) && window.setTimeout(function () {
            s(!0)
        }, 1)
    };
    scheduler.attachEvent("onViewChange", i), scheduler.attachEvent("onXLE", i), scheduler.attachEvent("onEventChanged", i), scheduler.attachEvent("onEventCreated", i), scheduler.attachEvent("onEventAdded", i), scheduler.attachEvent("onEventDeleted", i), scheduler.attachEvent("onAfterSchedulerResize", i), scheduler.attachEvent("onClearAll", i)
}();
//# sourceMappingURL=../sources/ext/dhtmlxscheduler_container_autoresize.js.map