/**
	http://www.brew365.com/ibu_calculator.php
**/
      function fillAA(RowNumber){
        var aaIdx =  (4*((RowNumber*1)-1)) + 3;
        var selectedIBUIdx = document.form1.elements[aaIdx-1].selectedIndex;
        var selectedIBU = document.form1.elements[aaIdx-1].options[selectedIBUIdx].value;
        document.form1.elements[aaIdx].value = selectedIBU;
        calcBitter();
        return true;
      }
 
      
      function calcBitter() {
            fixBlanks();
            var i = 0;
            var ttlIBU_Tinseth = 0.00;
            var ttlIBU_Rager = 0.00;
            var gallons = parseFloat(document.form1.elements[0].value);
            var gravity = parseFloat(document.form1.elements[1].value);

            
            //make gallons or gravity red if they're zero
            document.form1.elements[0].style.color = 'black';
            document.form1.elements[1].style.color = 'black';
            if(gallons == 0 || document.form1.elements[0].value==''){document.form1.elements[0].style.color = 'red';}
            if(gravity == 0 || document.form1.elements[1].value==''){document.form1.elements[1].style.color = 'red';}

            for (i=1; i<6; i++) {
                var aaIdx = (4*((i*1)-1)) + 3;
                var weightIdx = (4*((i*1)-1)) + 4;
                var timeIdx = (4*((i*1)-1)) + 5;
                thisAA = document.form1.elements[aaIdx].value;
                thisWeight = document.form1.elements[weightIdx].value;
                thisTime = document.form1.elements[timeIdx].value;
                var thisIBU_Tinseth = 0.00;
                var thisIBU_Rager = 0.00;

                //make sure all colors for this row are set back to black
                document.form1.elements[aaIdx].style.color = 'black';
                document.form1.elements[weightIdx].style.color = 'black';
                document.form1.elements[timeIdx].style.color = 'black';
                
                //calculate IBU if there are no zero values for this row
                if(thisAA == 0 || thisWeight == 0 || thisTime == 0){ 
                   thisIBU_Tinseth = 0;
                   thisIBU_Rager = 0;
                   if(thisAA==0 && !(thisWeight==0)){document.form1.elements[aaIdx].style.color = 'red';}
                   if(thisAA==0 && !(thisTime==0)){document.form1.elements[aaIdx].style.color = 'red';}
                   if(thisWeight==0 && !(thisAA==0)){document.form1.elements[weightIdx].style.color ='red';}
                   if(thisTime==0 && !(thisAA==0)){document.form1.elements[timeIdx].style.color = 'red';}                  
                }
                else
                {          
                  //tinseth formulas
                  //Utilization = (1.65*0.000125^(OG-1))*((1-2.72^(-0.04*Hop Boil Time))/4.14)
                  //IBU = Utilization *(oz*(AA% / 100)* 7490) / Volume of Batch in Gallons
                  //mgperl[i] = alpha[i]*mass[i]*7490/volume;
                  //util[i] = 1.65*Math.pow(0.000125, gravity)*(1-Math.exp(-0.04*time[i]))/4.15;
                  //rager formulas
                  // IBU = (Wt * util * AA% * 7489)/(volume * gravity)

                  var util = (1.65 * Math.pow(0.000125,gravity-1))*((1-Math.exp(-0.04*thisTime))/4.15);
                  util = util.toPrecision(5);
                  thisIBU_Tinseth = util *(thisWeight*(thisAA/ 100)* 7490) / gallons;
                  thisIBU_Rager = (thisWeight * util *(thisAA/100) * 7489)/(gallons * gravity);                
                  
                }                
                ttlIBU_Tinseth = ttlIBU_Tinseth + thisIBU_Tinseth;
                ttlIBU_Rager = ttlIBU_Rager + thisIBU_Rager;                
            }
            document.getElementById('ibu_tinseth').innerHTML = 'IBU (Tinseth): ' + ttlIBU_Tinseth.toPrecision(4); 
            document.getElementById('ibu_rager').innerHTML = 'IBU (Rager): ' + ttlIBU_Rager.toPrecision(4); 
            return true;
            
      }

      function fixBlanks(){
            for (i=1; i<6; i++) {
                var aaIdx = (4*((i*1)-1)) + 3;
                var weightIdx = (4*((i*1)-1)) + 4;
                var timeIdx = (4*((i*1)-1)) + 5;
                if(document.form1.elements[aaIdx].value==""){document.form1.elements[aaIdx].value=0;}
                if(document.form1.elements[weightIdx].value==""){document.form1.elements[weightIdx].value=0;} 
                if(document.form1.elements[timeIdx].value==""){document.form1.elements[timeIdx].value=0;}
            }      
      }            
