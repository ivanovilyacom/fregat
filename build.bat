sencha app build

set CUR_DIR=%CD%

md %CUR_DIR%\build\production\frigate\server
xcopy /s/y/q %CUR_DIR%\server %CUR_DIR%\build\production\frigate\server

@pause