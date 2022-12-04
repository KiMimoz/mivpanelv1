#include <amxmodx>
#include <fakemeta>
#include <sqlx>

#define PLUGIN "Panel Manager" 
#define VERSION "1.0" 
#define AUTHOR "Syko"

new HOST[]	= "sykoegay";
new USER[]	= "sykoegay";
new PASS[]	= "sykoegay";
new DATA[]	= "sykoegay";

//SQL
new Handle:g_SqlTuple;
new g_Error[512];
new g_szTemp[512];

//CHANGENAME
new g_name[] = { "name" };
new g_name_change[] = { "#Cstrike_Name_Change" };

new g_msgid_saytext;

public plugin_init()
{
	register_plugin(PLUGIN, VERSION, AUTHOR);

	g_msgid_saytext = get_user_msgid("SayText");

	register_forward(FM_ClientUserInfoChanged, "forward_client_userinfochanged");

	set_task(1.0, "MySql_Init"); // set a task to activate the mysql_init;
}
public MySql_Init()
{
	g_SqlTuple = SQL_MakeDbTuple(HOST, USER, PASS, DATA);
	new ErrorCode;
	new Handle:SqlConnection = SQL_Connect(g_SqlTuple, ErrorCode, g_Error, charsmax(g_Error));
	if (!SqlConnection)
	{
		log_to_file("sql_error.log", "Eroare conexiune SQL!");
	}
	new Handle:Queries = SQL_PrepareQuery(SqlConnection, "CREATE TABLE IF NOT EXISTS `admins` (`auth` varchar(32),`password` INT(11))");
	if (!SQL_Execute(Queries))
	{
		log_to_file("sql_error.log", "Eroare conexiune SQL!");
		SQL_QueryError(Queries, g_Error, charsmax(g_Error));
	}
	SQL_FreeHandle(Queries);
	SQL_FreeHandle(SqlConnection);
}
public plugin_end()
{
    SQL_FreeHandle(g_SqlTuple);
}
public forward_client_userinfochanged(id, buffer) {
	if(!is_user_connected(id)) {
		return FMRES_IGNORED;
	}
	static oldname[32], newname[32];
	get_user_name(id, oldname, sizeof oldname - 1);
	engfunc(EngFunc_InfoKeyValue, buffer, g_name, newname, sizeof newname - 1);
	if(equal(newname, oldname)) {
		return FMRES_IGNORED;
	}
	msg_name_change(id, oldname, newname);
	set_offline(id);
	set_task(3.0,"setonline", id);
	return FMRES_SUPERCEDE;
}
msg_name_change(id, /* const */ oldname[], /* const */ newname[]) {
	message_begin(MSG_BROADCAST, g_msgid_saytext)
	write_byte(id)
	write_string(g_name_change)
	write_string(oldname)
	write_string(newname)
	message_end()
}
public client_putinserver(id)
{
	if(!is_user_bot(id)) {
		set_online(id);
	}
}
public set_online(id)
{
	new sqlName[96], sqlIP[32], sqlAuthID[32];
	get_user_name(id, sqlName, charsmax(sqlName));
	get_user_ip(id, sqlIP, charsmax(sqlIP), 1);
	get_user_authid(id, sqlAuthID, charsmax(sqlAuthID));
	mysql_escape_string(sqlName, charsmax(sqlName));
	format(g_szTemp, charsmax(g_szTemp), "UPDATE `admins` SET `online` = '1', `LastIP` = '%s', `SteamID` = '%s' WHERE `admins`.`auth` LIKE '%s';", sqlIP, sqlAuthID, sqlName);
	SQL_ThreadQuery(g_SqlTuple, "IgnoreHandle", g_szTemp);
}
public set_offline(id)
{
	new sqlName[96];
	get_user_name(id, sqlName, charsmax(sqlName));
	mysql_escape_string(sqlName, charsmax(sqlName));
	format(g_szTemp, charsmax(g_szTemp), "UPDATE `admins` SET `online` = '0' WHERE `admins`.`auth` LIKE '%s';UPDATE `admins` SET `last_time` = CURDATE() WHERE `admins`.`auth` LIKE '%s';", sqlName, sqlName);
	SQL_ThreadQuery(g_SqlTuple, "IgnoreHandle", g_szTemp);
}
public client_disconnected(id)
{
	set_offline(id);
}
public IgnoreHandle(FailState, Handle:Query, Error[], Errcode, Data[], DataSize)
{
    SQL_FreeHandle(Query);
    return PLUGIN_HANDLED;
}

mysql_escape_string(dest[], len)
{
	replace_all(dest, len, "\\","\\\\");
	replace_all(dest, len, "\0","\\0");
	replace_all(dest, len, "\n","\\n");
	replace_all(dest, len, "\r","\\r");
	replace_all(dest, len, "\x1a","\Z");
	replace_all(dest, len, "'","''");
	replace_all(dest, len, "^"","^"^"");
}