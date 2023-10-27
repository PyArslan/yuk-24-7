$(document).ready(function()
{
   $("#RadioButton2").change(function()
   {
      if ($('#RadioButton2').is(':checked'))
      {
         $('#Summa').val(0);
         $('#Sumuser').val(350);
      }
   });
   $("#RadioButton2").trigger('change');
   $("#RadioButton1").change(function()
   {
      if ($('#RadioButton1').is(':checked'))
      {
         $('#Summa').val(0);
         $('#Sumuser').val(550);
      }
   });
   $("#RadioButton1").trigger('change');
   $("#RadioButton3").change(function()
   {
      if ($('#RadioButton3').is(':checked'))
      {
         $('#Summa').val(0);
         $('#Sumuser').val(750);
      }
   });
   $("#RadioButton3").trigger('change');
   $("#RadioButton4").change(function()
   {
      if ($('#RadioButton4').is(':checked'))
      {
         $('#Summa').val(0);
         $('#Sumuser').val(950);
      }
   });
   $("#RadioButton4").trigger('change');
   $("#Kolmes, #Sumuser, #Skidkapr, #Sumuser2").change(function()
   {
      $('#Skidkapr').val($('#Kolmes').val() * 5 - 5);
      $('#Sumuser2').val($('#Sumuser').val() / 100 * $('#Skidkapr').val() );
      $('#Summa').val(($('#Sumuser').val() - $('#Sumuser2').val()) * $('#Kolmes').val());
   });
   $("#Kolmes").trigger('change');
});
