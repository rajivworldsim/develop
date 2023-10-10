define([
    'jquery',
    'Mirasvit_CacheWarmer/js/lib/Chart.min',
    'moment'
], function ($, Chart, moment) {
    $.widget('mst.warmerChart', {
        options: {
            id: '',
            type: '',
            data: null,
            chartOptions: null,
        },

        id: null,
        type: null,
        data: null,
        chartOptions: null,

        _create: function () {
            this.id = this.options.id;
            this.type = this.options.type;
            this.data = this.options.data;
            this.chartOptions = this.options.chartOptions;

            if (this.type === 'line') {
                var yAxesConfig = this.chartOptions.scales.yAxes[0];

                yAxesConfig.ticks.callback = function (value, index, values) {
                    return value + '%';
                }

                this.chartOptions.tooltips.callbacks = {
                    label: function (tooltipItem, data) {
                        return tooltipItem.yLabel + '%';
                    },
                    title: function (tooltipItems, data) {
                        return moment(tooltipItems[0].xLabel).format('MMMM Do YYYY, H:mm');
                    }
                }

                this.chartOptions.tooltips.displayColors = false;

                this.chartOptions.scales.yAxes = [yAxesConfig];

                if (this.id == 'serverLoad') {
                    var ctx = document.getElementById(this.id).getContext('2d');

                    var gradient = ctx.createLinearGradient(0,100,0,0);
                    gradient.addColorStop(1, 'rgb(244,67,54)');
                    gradient.addColorStop(0.9, 'rgb(244,67,54)');
                    gradient.addColorStop(0.8, 'rgb(252,220,37)');
                    gradient.addColorStop(0.6, 'rgb(252,220,37)');
                    gradient.addColorStop(0.4, 'rgb(71,205,74)');
                    gradient.addColorStop(0, 'rgb(71,205,74)');

                    var dataset = this.data.datasets[0];

                    dataset.backgroundColor = gradient;

                    this.data.datasets[0] = dataset;
                }
            } else {
                this.chartOptions.tooltips.callbacks = {
                    label: function (tooltipItem, data) {
                        return data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index] + '%';
                    }
                }

                this.chartOptions.tooltips.mode = 'point';
            }

            var chartConfig = {
                type: this.type,
                data: this.data,
                options: this.chartOptions
            }

            var chart = new Chart(
                $("#" + this.id),
                chartConfig
            );
        }
    });

    return $.mst.warmerChart;
});
